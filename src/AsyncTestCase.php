<?php

declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use React\EventLoop\Loop;
use React\Promise\PromiseInterface;
use WyriHaximus\TestUtilities\TestCase;

use function Clue\React\Block\await;
use function Clue\React\Block\awaitAll;
use function Clue\React\Block\awaitAny;
use function spl_object_hash;

abstract class AsyncTestCase extends TestCase
{
    public const DEFAULT_AWAIT_TIMEOUT = 6.0;

    private const INVOKE_ARRAY = ['__invoke'];

    /**
     * @return mixed returns whatever the promise resolves to
     *
     * @psalm-suppress MissingReturnType
     *
     * @codingStandardsIgnoreStart
     */
    final protected function await(PromiseInterface $promise, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT)
    {
        return await($promise, Loop::get(), $timeout);
    }

    /**
     * @param  PromiseInterface[] $promises
     *
     * @return mixed[]
     */
    final protected function awaitAll(array $promises, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT): array
    {
        return awaitAll($promises, Loop::get(), $timeout);
    }

    /**
     * @param  PromiseInterface[] $promises
     *
     * @return mixed
     *
     * @psalm-suppress MissingReturnType
     *
     * @codingStandardsIgnoreStart
     */
    final protected function awaitAny(array $promises, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT)
    {
        return awaitAny($promises, Loop::get(), $timeout);
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
        $method = $mock
            ->expects($invokedCount)
            ->method('__invoke');

        /**
         * Trick to keep infection from dropping the above line.
         */
        $id = spl_object_hash($method);
        $di = $id;

        /** @psalm-suppress InvalidReturnStatement */
        return $mock;
    }
}
