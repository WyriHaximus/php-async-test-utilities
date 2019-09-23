<?php declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use function Clue\React\Block\await;
use function Clue\React\Block\awaitAll;
use function Clue\React\Block\awaitAny;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
use React\Promise\PromiseInterface;
use WyriHaximus\TestUtilities\TestCase;

abstract class AsyncTestCase extends TestCase
{
    public const DEFAULT_AWAIT_TIMEOUT = 60.0;

    public function provideEventLoop(): iterable
    {
        yield [null];
        yield [Factory::create()];
        yield [new StreamSelectLoop()];
    }

    /**
     * @param  PromiseInterface   $promise
     * @param  LoopInterface|null $loop
     * @param  float|null         $timeout
     * @return mixed
     */
    protected function await(PromiseInterface $promise, ?LoopInterface $loop = null, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT)
    {
        if (!($loop instanceof LoopInterface)) {
            $loop = Factory::create();
        }

        return await($promise, $loop, $timeout);
    }

    /**
     * @param  PromiseInterface[] $promises
     * @param  LoopInterface|null $loop
     * @param  float|null         $timeout
     * @return mixed[]
     */
    protected function awaitAll(array $promises, ?LoopInterface $loop = null, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT): array
    {
        if (!($loop instanceof LoopInterface)) {
            $loop = Factory::create();
        }

        return awaitAll($promises, $loop, $timeout);
    }

    /**
     * @param  PromiseInterface[] $promises
     * @param  LoopInterface|null $loop
     * @param  float|null         $timeout
     * @return mixed
     */
    protected function awaitAny(array $promises, ?LoopInterface $loop = null, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT)
    {
        if (!($loop instanceof LoopInterface)) {
            $loop = Factory::create();
        }

        return awaitAny($promises, $loop, $timeout);
    }
}
