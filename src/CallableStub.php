<?php declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

abstract class CallableStub
{
    public function __invoke(): void
    {
    }
}
