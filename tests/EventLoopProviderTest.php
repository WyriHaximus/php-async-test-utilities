<?php declare(strict_types=1);

namespace WyriHaximus\Tests\AsyncTestUtilities;

use React\EventLoop\LoopInterface;
use WyriHaximus\TestUtilities\TestCase;
use function WyriHaximus\iteratorOrArrayToArray;

final class EventLoopProviderTest extends TestCase
{
    public function testProvidedEventLoopCount(): void
    {
        $eventLoops = iteratorOrArrayToArray((new AsyncTestCaseTest())->provideEventLoop());

        self::assertCount(3, $eventLoops);
        self::assertCount(1, array_filter($eventLoops, function ($eventLoop) {
            return $eventLoop[0] instanceof LoopInterface === false;
        }));
        self::assertCount(2, array_filter($eventLoops, function ($eventLoop) {
            return $eventLoop[0] !== null;
        }));
    }
}
