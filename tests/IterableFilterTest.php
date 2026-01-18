<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use Generator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\collect;
use function Anarchitecture\pipe\iterable_filter;

final class IterableFilterTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = iterable_filter(static fn (mixed $_v, mixed $_k) : bool => true);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_filters_values_and_preserves_keys() : void {

        $stage = [
            10  => 1,
            20  => 0,
            'x' => 2,
        ];

        $result = $stage
                |> iterable_filter(static fn (int $v, int|string $k) : bool => $v > 0)
                |> collect(...);

        self::assertSame([
            10  => 1,
            'x' => 2,
        ], $result);
    }

    public function test_predicate_receives_key_and_can_filter_by_key() : void {

        $stage = [
            'keep' => 1,
            'drop' => 2,
        ];

        $result = $stage
            |> iterable_filter(static fn (int $_v, string $k) : bool => $k === 'keep')
            |> collect(...);

        self::assertSame([
            'keep' => 1,
        ], $result);
    }

    public function test_works_with_generators_and_preserves_keys() : void {

        $stage = (function () : Generator {
            yield 'a' => 1;
            yield 'b' => 2;
            yield 'c' => 3;
        })();

        $result = $stage
            |> iterable_filter(static fn (int $v, string $_k) : bool => $v % 2 === 1)
            |> collect(...);

        self::assertSame([
            'a' => 1,
            'c' => 3,
        ], $result);
    }

    public function test_is_lazy_and_does_not_call_predicate_until_iterated() : void {

        $calls = 0;

        $predicate = static function (mixed $_v, mixed $_k) use (&$calls) : bool {
            $calls++;
            return true;
        };

        $filtered = [1, 2, 3]
            |> iterable_filter($predicate);

        self::assertSame(0, $calls);

        $result = $filtered
            |> collect(...);

        self::assertSame([1, 2, 3], $result);
        self::assertSame(3, $calls);
    }

    public function test_empty_iterable_yields_empty_and_does_not_call_predicate() : void {

        $calls = 0;

        $predicate = static function (mixed $_v, mixed $_k) use (&$calls) : bool {
            $calls++;
            return true;
        };

        $result = []
            |> iterable_filter($predicate)
            |> collect(...);

        self::assertSame([], $result);
        self::assertSame(0, $calls);
    }

    public function test_preserves_sparse_numeric_keys() : void {

        $stage = [
            2  => 'a',
            10 => 'b',
            11 => 'c',
        ];

        $result = $stage
                |> iterable_filter(static fn (string $v, int $_k) : bool => $v !== 'b')
                |> collect(...);

        self::assertSame([
            2  => 'a',
            11 => 'c',
        ], $result);
    }

}