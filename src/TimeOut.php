<?php

declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final readonly class TimeOut
{
    public function __construct(
        public int|float $timeout,
    ) {
    }
}
