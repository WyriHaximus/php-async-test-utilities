<?php declare(strict_types=1);

namespace WyriHaximus\Tests\AsyncTestUtilities;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\Timer\TimeoutException;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use function React\Promise\resolve;
use function time;

final class AsyncTestCaseTest extends AsyncTestCase
{
    /**
     * @dataProvider provideEventLoop
     */
    public function testAwait(?LoopInterface $loop): void
    {
        $value = time();
        static::assertSame($value, $this->await(resolve($value), $loop));
    }

    /**
     * @dataProvider provideEventLoop
     */
    public function testAwaitAll(?LoopInterface $loop): void
    {
        $value = time();
        static::assertSame([$value, $value], $this->awaitAll([resolve($value), resolve($value)], $loop));
    }

    /**
     * @dataProvider provideEventLoop
     */
    public function testAwaitAny(?LoopInterface $loop): void
    {
        $value = time();
        static::assertSame($value, $this->awaitAny([resolve($value), resolve($value)], $loop));
    }

    /**
     * @dataProvider provideEventLoop
     */
    public function testAwaitTimeout(?LoopInterface $loop): void
    {
        self::expectException(TimeoutException::class);

        $this->await((new Deferred())->promise(), $loop, 0.1);
    }

    public function testExpectCallableExactly(): void
    {
        $callable = $this->expectCallableExactly(3);

        $loop = Factory::create();
        $loop->futureTick($callable);
        $loop->futureTick($callable);
        $loop->futureTick($callable);
        $loop->run();
    }

    public function testExpectCallableOnce(): void
    {
        $loop = Factory::create();
        $loop->futureTick($this->expectCallableOnce());
        $loop->run();
    }

    public function testExpectCallableNever(): void
    {
        $loop = Factory::create();
        $loop->futureTick($this->expectCallableNever());
    }
}
