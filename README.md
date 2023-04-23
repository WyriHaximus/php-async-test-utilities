# Test utilities

![Continuous Integration](https://github.com/wyrihaximus/php-async-test-utilities/workflows/Continuous%20Integration/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/wyrihaximus/test-utilities/v/stable.png)](https://packagist.org/packages/wyrihaximus/test-utilities)
[![Total Downloads](https://poser.pugx.org/wyrihaximus/test-utilities/downloads.png)](https://packagist.org/packages/wyrihaximus/test-utilities/stats)
[![Code Coverage](https://scrutinizer-ci.com/g/WyriHaximus/php-async-test-utilities/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/WyriHaximus/php-async-test-utilities/?branch=master)
[![Type Coverage](https://shepherd.dev/github/WyriHaximus/php-async-test-utilities/coverage.svg)](https://shepherd.dev/github/WyriHaximus/php-async-test-utilities)
[![License](https://poser.pugx.org/wyrihaximus/test-utilities/license.png)](https://packagist.org/packages/wyrihaximus/test-utilities)

# Installation

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require wyrihaximus/async-test-utilities
```

# Usage

Any test file can extend `WyriHaximus\AsyncTestUtilities\TestCase` and it comes with some goodies such as random
namespaces and random directories to use for file storage related tests.

Since all tests are executed inside a fiber, there is a default timeout of `30` seconds. To lower or raise that timeout
this package comes with a `TimeOut` attribute. It can be set on the class and method level. When set on both the method level it takes priority over the class level.

```php
<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\AsyncTestUtilities;

use React\EventLoop\Loop;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\AsyncTestUtilities\TimeOut;

use function React\Async\async;
use function React\Async\await;
use function React\Promise\resolve;
use function React\Promise\Timer\sleep;
use function time;

#[TimeOut(0.3)]
final class AsyncTestCaseTest extends AsyncTestCase
{
    #[TimeOut(1)]
    public function testAllTestsAreRanInAFiber(): void
    {
        self::expectOutputString('ab');

        Loop::futureTick(async(static function (): void {
            echo 'a';
        }));

        await(sleep(1));

        echo 'b';
    }

    public function testExpectCallableExactly(): void
    {
        $callable = $this->expectCallableExactly(3);

        Loop::futureTick($callable);
        Loop::futureTick($callable);
        Loop::futureTick($callable);
    }

    public function testExpectCallableOnce(): void
    {
        Loop::futureTick($this->expectCallableOnce());
    }
}
```

# License

The MIT License (MIT)

Copyright (c) 2023 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
