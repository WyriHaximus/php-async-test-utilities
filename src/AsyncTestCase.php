<?php

declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use WyriHaximus\React\PHPUnit\RunTestsInFibersTrait;
use WyriHaximus\TestUtilities\TestCase;

abstract class AsyncTestCase extends TestCase
{
    use RunTestsInFibersTrait;
}
