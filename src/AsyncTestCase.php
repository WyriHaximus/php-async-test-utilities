<?php

declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use React\EventLoop\Loop;
use ReflectionClass;
use WyriHaximus\TestUtilities\TestCase;

use function React\Async\async;
use function React\Async\await;

abstract class AsyncTestCase extends TestCase
{
    private const INVOKE_ARRAY = ['__invoke'];

    private string|null $realTestName = null;

    /** @deprecated With the move to fibers there is no need for these rarely used methods anymore. (Unless proven otherwise of course.) */
    final protected function expectCallableExactly(int $amount): callable
    {
        return $this->getCallableMock(self::exactly($amount));
    }

    /** @deprecated With the move to fibers there is no need for these rarely used methods anymore. (Unless proven otherwise of course.) */
    final protected function expectCallableOnce(): callable
    {
        return $this->getCallableMock(self::once());
    }

    /** @deprecated With the move to fibers there is no need for these rarely used methods anymore. (Unless proven otherwise of course.) */
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

    /** @codeCoverageIgnore Invoked before code coverage data is being collected. */
    final public function setName(string $name): void
    {
        /** @psalm-suppress InternalMethod */
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

        $timeout         = 30;
        $reflectionClass = new ReflectionClass($this::class);
        foreach ($reflectionClass->getAttributes() as $classAttribute) {
            $classTimeout = $classAttribute->newInstance();
            if (! ($classTimeout instanceof TimeOut)) {
                continue;
            }

            $timeout = $classTimeout->timeout;
        }

        /**
         * @psalm-suppress InternalMethod
         * @psalm-suppress PossiblyNullArgument
         */
        foreach ($reflectionClass->getMethod($this->realTestName)->getAttributes() as $methodAttribute) {
            $methodTimeout = $methodAttribute->newInstance();
            if (! ($methodTimeout instanceof TimeOut)) {
                continue;
            }

            $timeout = $methodTimeout->timeout;
        }

        $timeout = Loop::addTimer($timeout, static fn () => Loop::stop());

        try {
            /**
             * @psalm-suppress MixedArgument
             * @psalm-suppress UndefinedInterfaceMethod
             */
            return await(async(
                fn (): mixed => ([$this, $this->realTestName])(...$args),
            )());
        } finally {
            Loop::cancelTimer($timeout);
        }
    }

    final protected function runTest(): mixed
    {
        /** @psalm-suppress InternalMethod */
        parent::setName('runAsyncTest');

        return parent::runTest();
    }
}
