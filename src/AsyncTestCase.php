<?php declare(strict_types=1);

namespace WyriHaximus\AsyncTestUtilities;

use function Clue\React\Block\await;
use function Clue\React\Block\awaitAll;
use function Clue\React\Block\awaitAny;
use PHPUnit\Framework\MockObject\Matcher\InvokedCount;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
use React\Promise\PromiseInterface;
use WyriHaximus\TestUtilities\TestCase;

abstract class AsyncTestCase extends TestCase
{
    public const DEFAULT_AWAIT_TIMEOUT = 60.0;

    private const INVOKE_ARRAY = ['__invoke'];

    public function provideEventLoop(): iterable
    {
        yield [null];
        yield [Factory::create()];
        yield [new StreamSelectLoop()];
    }

    /**
     * @param  PromiseInterface   $promise
     * @param  LoopInterface|null $loop
     * @param  float|null         $timeout
     * @return mixed
     */
    protected function await(PromiseInterface $promise, ?LoopInterface $loop = null, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT)
    {
        if (!($loop instanceof LoopInterface)) {
            $loop = Factory::create();
        }

        return await($promise, $loop, $timeout);
    }

    /**
     * @param  PromiseInterface[] $promises
     * @param  LoopInterface|null $loop
     * @param  float|null         $timeout
     * @return mixed[]
     */
    protected function awaitAll(array $promises, ?LoopInterface $loop = null, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT): array
    {
        if (!($loop instanceof LoopInterface)) {
            $loop = Factory::create();
        }

        return awaitAll($promises, $loop, $timeout);
    }

    /**
     * @param  PromiseInterface[] $promises
     * @param  LoopInterface|null $loop
     * @param  float|null         $timeout
     * @return mixed
     */
    protected function awaitAny(array $promises, ?LoopInterface $loop = null, ?float $timeout = self::DEFAULT_AWAIT_TIMEOUT)
    {
        if (!($loop instanceof LoopInterface)) {
            $loop = Factory::create();
        }

        return awaitAny($promises, $loop, $timeout);
    }

    protected function expectCallableExactly(int $amount): callable
    {
        return $this->getCallableMock(self::exactly($amount));
    }

    protected function expectCallableOnce(): callable
    {
        return $this->getCallableMock(self::once());
    }

    protected function expectCallableNever(): callable
    {
        return $this->getCallableMock(self::never());
    }

    private function getCallableMock(InvokedCount $invokedCount): callable
    {
        $mock = $this->getMockBuilder(CallableStub::class)->onlyMethods(self::INVOKE_ARRAY)->getMock();
        /** @psalm-suppress InternalMethod */
        $method = $mock
            ->expects($invokedCount)
            ->method('__invoke');

        /**
         * Trick to keep infection from dropping the above line.
         *
         * @var string $id
         */
        $id = \spl_object_hash($method);

        /** @var callable $mock */
        $mock = $mock;

        return $mock;
    }
}
