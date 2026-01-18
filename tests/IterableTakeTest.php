<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use Generator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\collect;
use function Anarchitecture\pipe\iterable_take;

final class IterableTakeTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = iterable_take(1);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_takes_first_n_items_and_preserves_keys() : void {

        $stage = [
            10  => 'a',
            20  => 'b',
            'x' => 'c',
        ];

        $result = $stage
            |> iterable_take(2)
            |> collect(...);

        self::assertSame([
            10 => 'a',
            20 => 'b',
        ], $result);
    }

    public function test_when_count_exceeds_length_it_returns_all_items() : void {

        $stage = [1, 2, 3];

        $result = $stage
            |> iterable_take(10)
            |> collect(...);

        self::assertSame([1, 2, 3], $result);
    }

    public function test_take_zero_yields_empty_and_does_not_consume_input() : void {

        $calls = 0;

        $stage = (function () use (&$calls) : Generator {
            $calls++;
            yield 1;
            $calls++;
            yield 2;
        })();

        $result = $stage
            |> iterable_take(0)
            |> collect(...);

        self::assertSame([], $result);
        self::assertSame(0, $calls);
    }

    public function test_short_circuits_and_does_not_consume_more_than_count() : void {

        $calls = 0;

        $stage = (function () use (&$calls) : Generator {
            for ($i = 1; $i <= 5; $i++) {
                $calls++;
                yield $i;
            }
        })();

        $result = $stage
            |> iterable_take(2)
            |> collect(...);

        self::assertSame([1, 2], $result);
        self::assertSame(2, $calls);
    }

    public function test_preserves_sparse_numeric_keys() : void {

        $stage = [
            2  => 'a',
            10 => 'b',
            11 => 'c',
        ];

        $result = $stage
            |> iterable_take(2)
            |> collect(...);

        self::assertSame([
            2  => 'a',
            10 => 'b',
        ], $result);
    }

    public function test_take_negative_throws_invalid_argument_exception() : void {

        $this->expectException(\InvalidArgumentException::class);
        iterable_take(-1);
    }

}
