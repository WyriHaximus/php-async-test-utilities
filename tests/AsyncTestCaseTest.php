<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\AsyncTestUtilities;

use Fiber;
use PHPUnit\Framework\Attributes\Test;
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
    #[Test]
    public function allTestsAreRanInAFiber(): void
    {
        self::expectOutputString('ab');

        Loop::futureTick(async(static function (): void {
            echo 'a';
        }));

        await(sleep(0.01));

        echo 'b';
    }

    #[Test]
    public function fiberGetCurrentReturnsAFiberInstance(): void
    {
        self::assertInstanceOf(Fiber::class, Fiber::getCurrent());
    }
}
