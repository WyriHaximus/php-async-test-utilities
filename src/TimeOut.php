<?php

declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use Attribute;
use WyriHaximus\React\PHPUnit\TimeOutInterface;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final readonly class TimeOut implements TimeOutInterface
{
    /** @api */
    public function __construct(
        public int|float $timeout,
    ) {
    }

    public function timeout(): int|float
    {
        return $this->timeout;
    }
}
