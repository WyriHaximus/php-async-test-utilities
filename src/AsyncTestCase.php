<?php

declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use Generator;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
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
     * @return Generator<array<int, LoopInterface|StreamSelectLoop|null>>
     */
    final public function provideEventLoop(): Generator
    {
        yield [null];
        yield [Factory::create()];
        yield [new StreamSelectLoop()];
    }

    /**
     * @return mixed returns whatever the promise resolves to
     *
     * @psalm-suppress MissingReturnType
     *
     * @codingStandardsIgnoreStart
     */
    final protected function await(PromiseInterface $promise, ?LoopInterface $loop = null, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT)
    {
        if (! ($loop instanceof LoopInterface)) {
            $loop = Factory::create();
        }

        return await($promise, $loop, $timeout);
    }

    /**
     * @param  PromiseInterface[] $promises
     *
     * @return mixed[]
     */
    final protected function awaitAll(array $promises, ?LoopInterface $loop = null, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT): array
    {
        if (! ($loop instanceof LoopInterface)) {
            $loop = Factory::create();
        }

        return awaitAll($promises, $loop, $timeout);
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
    final protected function awaitAny(array $promises, ?LoopInterface $loop = null, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT)
    {
        if (! ($loop instanceof LoopInterface)) {
            $loop = Factory::create();
        }

        return awaitAny($promises, $loop, $timeout);
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
