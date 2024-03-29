<?php

declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use WyriHaximus\React\PHPUnit\RunTestsInFibersTrait;
use WyriHaximus\TestUtilities\TestCase;

abstract class AsyncTestCase extends TestCase
{
    use RunTestsInFibersTrait;

    private const INVOKE_ARRAY = ['__invoke'];

    /** @deprecated With the move to fibers there is no need for these rarely used methods anymore. (Unless proven otherwise of course.) */
    final protected function expectCallableExactly(int $amount): callable
    {
        return $this->getCallableMock(self::exactly($amount));
    }

    /** @deprecated With the move to fibers there is no need for these rarely used methods anymore. (Unless proven otherwise of course.) */
    final protected function expectCallableOnce(): callable
    {
        return $this->getCallableMock(self::once());
    }

    /** @deprecated With the move to fibers there is no need for these rarely used methods anymore. (Unless proven otherwise of course.) */
    final protected function expectCallableNever(): callable
    {
        return $this->getCallableMock(self::never());
    }

    /** @psalm-suppress InvalidReturnType */
    private function getCallableMock(InvokedCount $invokedCount): callable
    {
        $mock = $this->getMockBuilder(CallableStub::class)->onlyMethods(self::INVOKE_ARRAY)->getMock();
        /** @psalm-suppress InternalMethod */
        $mock
            ->expects($invokedCount)
            ->method('__invoke');

        /** @psalm-suppress InvalidReturnStatement */
        return $mock;
    }
}
