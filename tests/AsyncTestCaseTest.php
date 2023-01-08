<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\AsyncTestUtilities;

use React\EventLoop\Loop;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;

use function React\Promise\resolve;
use function time;

final class AsyncTestCaseTest extends AsyncTestCase
{
    public function testAwait(): void
    {
        $value = time();
        static::assertSame($value, $this->await(resolve($value)));
    }

    public function testAwaitAll(): void
    {
        $value = time();
        static::assertSame([$value, $value], $this->awaitAll(resolve($value), resolve($value)));
    }

    public function testAwaitAny(): void
    {
        $value = time();
        static::assertSame($value, $this->awaitAny(resolve($value), resolve($value)));
    }

    public function testExpectCallableExactly(): void
    {
        $callable = $this->expectCallableExactly(3);

        Loop::futureTick($callable);
        Loop::futureTick($callable);
        Loop::futureTick($callable);
        Loop::run();
    }

    public function testExpectCallableOnce(): void
    {
        Loop::futureTick($this->expectCallableOnce());
        Loop::run();
    }
}
