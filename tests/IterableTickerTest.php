<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Generator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\collect;
use function Anarchitecture\pipe\iterable_take;
use function Anarchitecture\pipe\iterable_ticker;

final class IterableTickerTest extends TestCase {

    public function test_default_starts_at_zero_and_increments_by_one() : void {

        $result = iterable_ticker()
            |> iterable_take(5)
            |> collect(...);

        self::assertSame([
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        ], $result);
    }

    public function test_custom_start_affects_values_but_keys_still_start_at_zero() : void {

        $result = iterable_ticker(5)
            |> iterable_take(3)
            |> collect(...);

        self::assertSame([
            0 => 5,
            1 => 6,
            2 => 7,
        ], $result);
    }

    public function test_can_be_advanced_manually() : void {

        $ticker = iterable_ticker(10);

        self::assertInstanceOf(Generator::class, $ticker);

        self::assertSame(0, $ticker->key());
        self::assertSame(10, $ticker->current());

        $ticker->next();
        self::assertSame(1, $ticker->key());
        self::assertSame(11, $ticker->current());

        $ticker->next();
        self::assertSame(2, $ticker->key());
        self::assertSame(12, $ticker->current());
    }
}
