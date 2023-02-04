<?php

declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use React\Promise\PromiseInterface;
use WyriHaximus\TestUtilities\TestCase;

use function React\Async\await;
use function React\Promise\all;
use function React\Promise\any;

abstract class AsyncTestCase extends TestCase
{
    private const INVOKE_ARRAY = ['__invoke'];

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
}
