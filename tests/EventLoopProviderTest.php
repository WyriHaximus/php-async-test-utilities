<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\AsyncTestUtilities;

use React\EventLoop\LoopInterface;
use WyriHaximus\TestUtilities\TestCase;

use function array_filter;
use function count;
use function current;
use function WyriHaximus\iteratorOrArrayToArray;

final class EventLoopProviderTest extends TestCase
{
    public function testProvidedEventLoopCount(): void
    {
        $eventLoops = iteratorOrArrayToArray((new AsyncTestCaseTest())->provideEventLoop());

        self::assertCount(4, $eventLoops);
        self::assertCount(2, array_filter($eventLoops, static fn ($eventLoop): bool => count($eventLoop) === 0 || current($eventLoop) instanceof LoopInterface === false));
        self::assertCount(2, array_filter($eventLoops, static fn ($eventLoop): bool => count($eventLoop) !== 0 && current($eventLoop) !== null));
    }
}
