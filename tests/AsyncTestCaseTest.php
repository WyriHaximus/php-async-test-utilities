<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\AsyncTestUtilities;

use React\EventLoop\Loop;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\AsyncTestUtilities\TimeOut;

use function React\Async\async;
use function React\Async\await;
use function React\Promise\Timer\sleep;

#[TimeOut(1)]
final class AsyncTestCaseTest extends AsyncTestCase
{
    #[TimeOut(0.1)]
    public function testAllTestsAreRanInAFiber(): void
    {
        self::expectOutputString('ab');

        Loop::futureTick(async(static function (): void {
            echo 'a';
        }));

        await(sleep(0.01));

        echo 'b';
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
