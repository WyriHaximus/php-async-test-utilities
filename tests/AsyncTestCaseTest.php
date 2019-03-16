<?php declare(strict_types=1);

namespace WyriHaximus\Tests\AsyncTestUtilities;

use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
use React\Promise\Deferred;
use React\Promise\Timer\TimeoutException;
use function React\Promise\resolve;

final class AsyncTestCaseTest extends AsyncTestCase
{
    public function provideEventLoop(): iterable
    {
        yield [null];
        yield [Factory::create()];
        yield [new StreamSelectLoop()];
    }

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
}
