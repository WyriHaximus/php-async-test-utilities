<?php

declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use React\EventLoop\Loop;
use React\Promise\PromiseInterface;
use WyriHaximus\TestUtilities\TestCase;

use function React\Async\async;
use function React\Async\await;
use function React\Promise\all;
use function React\Promise\any;

abstract class AsyncTestCase extends TestCase
{
    private const INVOKE_ARRAY = ['__invoke'];

    private ?string $realTestName = null;

    /**
     * @return mixed returns whatever the promise resolves to
     *
     * @psalm-suppress MissingReturnType
     *
     * @codingStandardsIgnoreStart
     * @deprecated Use \React\Async\await directly
     */
    final protected function await(PromiseInterface $promise): mixed
    {
        return await($promise);
    }

    /**
     * @return array<mixed>
     * @deprecated Use \React\Async\await and \React\Promise\all directly
     */
    final protected function awaitAll(PromiseInterface ...$promises): array
    {
        /** @var array<mixed> */
        return await(all($promises));
    }

    /**
     * @return mixed
     *
     * @psalm-suppress MissingReturnType
     *
     * @codingStandardsIgnoreStart
     * @deprecated Use \React\Async\await and \React\Promise\any directly
     */
    final protected function awaitAny(PromiseInterface ...$promises): mixed
    {
        return await(any($promises));
    }

    final protected function expectCallableExactly(int $amount): callable
    {
        return $this->getCallableMock(self::exactly($amount));
    }

    final protected function expectCallableOnce(): callable
    {
        return $this->getCallableMock(self::once());
    }

    final protected function expectCallableNever(): callable
    {
        return $this->getCallableMock(self::never());
    }

    /** @psalm-suppress InvalidReturnType */
    private function getCallableMock(InvokedCount $invokedCount): callable
    {
        $mock = $this->getMockBuilder(CallableStub::class)->onlyMethods(self::INVOKE_ARRAY)->getMock();
        /** @psalm-suppress InternalMethod */
        $mock
            ->expects($invokedCount)
            ->method('__invoke');

        /** @psalm-suppress InvalidReturnStatement */
        return $mock;
    }

    /**
     * @codeCoverageIgnore Invoked before code coverage data is being collected.
     */
    final public function setName(string $name): void
    {
        /**
         * @psalm-suppress InternalMethod
         */
        parent::setName($name);
        $this->realTestName = $name;
    }

    /** @internal */
    final protected function runAsyncTest(mixed ...$args): mixed
    {
        /**
         * @psalm-suppress InternalMethod
         * @psalm-suppress PossiblyNullArgument
         */
        parent::setName($this->realTestName);
        $timeout = 30;
        $reflectionClass = new \ReflectionClass($this::class);
        foreach ($reflectionClass->getAttributes() as $classAttribute) {
            $classTimeout = $classAttribute->newInstance();
            if ($classTimeout instanceof TimeOut) {
                $timeout = $classTimeout->timeout;
            }
        }
        /**
         * @psalm-suppress InternalMethod
         * @psalm-suppress PossiblyNullArgument
         */
        foreach ($reflectionClass->getMethod($this->realTestName)->getAttributes() as $methodAttribute) {
            $methodTimeout = $methodAttribute->newInstance();
            if ($methodTimeout instanceof TimeOut) {
                $timeout = $methodTimeout->timeout;
            }
        }

        $timeout = Loop::addTimer($timeout, static fn () => Loop::stop());

        /**
         * @psalm-suppress MixedArgument
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return await(async(
            fn (): mixed => ([$this, $this->realTestName])(...$args),
        )()->always(static fn () => Loop::cancelTimer($timeout)));
    }

    final protected function runTest(): mixed
    {
        /**
         * @psalm-suppress InternalMethod
         */
        parent::setName('runAsyncTest');
        return parent::runTest();
    }
}
