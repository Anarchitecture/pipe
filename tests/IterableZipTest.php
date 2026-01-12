<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_map;
use function Anarchitecture\pipe\iterable_zip;

final class IterableZipTest extends TestCase {

    public function test_returns_a_closure() : void {

        self::assertInstanceOf(Closure::class, iterable_zip());
    }

    public function test_zips_left_with_one_right_iterable() : void {

        $left = [1, 2, 3];
        $right = [10, 20, 30];

        $result = $left
            |> iterable_zip($right)
            |> iterator_to_array(...);

        self::assertSame([
            0 => [1, 10],
            1 => [2, 20],
            2 => [3, 30],
        ], $result);
    }

    public function test_preserves_left_keys() : void {

        $left = ['a' => 1, 'b' => 2];
        $right = [10, 20];

        $result = $left
            |> iterable_zip($right)
            |> iterator_to_array(...);

        self::assertSame([
            'a' => [1, 10],
            'b' => [2, 20],
        ], $result);
    }

    public function test_stops_at_shortest_right_iterable() : void {

        $left = [1, 2, 3];
        $right = [10];

        $result = $left
            |> iterable_zip($right)
            |> iterator_to_array(...);

        self::assertSame([
            0 => [1, 10],
        ], $result);
    }

    public function test_zips_with_multiple_right_iterables() : void {

        $left = [1, 2];
        $right1 = [10, 20];
        $right2 = [100, 200];

        $result = $left
            |> iterable_zip($right1, $right2)
            |> iterator_to_array(...);

        self::assertSame([
            0 => [1, 10, 100],
            1 => [2, 20, 200],
        ], $result);
    }

    public function test_no_right_iterables_wraps_left_values() : void {

        $left = [1, 2];

        $result = $left
            |> iterable_zip()
            |> iterator_to_array(...);

        self::assertSame([
            0 => [1],
            1 => [2],
        ], $result);
    }

    public function test_accepts_generator_as_left_iterable() : void {

        $left = (static function () : \Generator {
            yield 10;
            yield 20;
        })();
        $right = [1, 2];

        $result = $left
            |> iterable_zip($right)
            |> iterator_to_array(...);

        self::assertSame([
            0 => [10, 1],
            1 => [20, 2],
        ], $result);
    }

    public function test_accepts_generator_as_right_iterable() : void {

        $left = [1, 2];
        $right = (static function () : \Generator {
            yield 10;
            yield 20;
        })();

        $result = $left
                |> iterable_zip($right)
                |> iterator_to_array(...);

        self::assertSame([
            0 => [1, 10],
            1 => [2, 20],
        ], $result);
    }

    public function test_accepts_iterator_as_left_iterable() : void {

        $left = new \ArrayIterator([10, 20]);
        $right  = [1, 2];

        $result = $left
                |> iterable_zip($right)
                |> iterator_to_array(...);

        self::assertSame([
            0 => [10, 1],
            1 => [20, 2],
        ], $result);
    }

    public function test_accepts_iterator_as_right_iterable() : void {

        $left  = [1, 2];
        $right = new \ArrayIterator([10, 20]);

        $result = $left
            |> iterable_zip($right)
            |> iterator_to_array(...);

        self::assertSame([
            0 => [1, 10],
            1 => [2, 20],
        ], $result);
    }

    public function test_accepts_mixed_iterator_types_as_right_iterables() : void {

        $left  = [1, 2];
        $right = [
            new \ArrayIterator([10, 20]),
            (static function () : \Generator {
                yield 100;
                yield 200;
            })(),
            [1000, 2000]
        ];


        $result = $left
                |> iterable_zip(...$right)
                |> iterator_to_array(...);

        self::assertSame([
            0 => [1, 10, 100, 1000],
            1 => [2, 20, 200, 2000],
        ], $result);
    }

    public function test_stops_at_shortest_left_iterable() : void {

        $left = [1, 2];
        $right = [10, 20, 30, 40];

        $result = $left
            |> iterable_zip($right)
            |> iterator_to_array(...);

        self::assertSame([
            0 => [1, 10],
            1 => [2, 20],
        ], $result);
    }

    public function test_stops_when_any_right_is_exhausted() : void {

        $left = [1, 2, 3];
        $right1 = [10, 20, 30, 40];
        $right2 = [100];

        $result = $left
            |> iterable_zip($right1, $right2)
            |> iterator_to_array(...);

        self::assertSame([
            0 => [1, 10, 100],
        ], $result);
    }

    public function test_zips_can_be_piped() : void {

        $left = ["one" => 1, "two" => 2];
        $right1 = [10, 20];
        $right2 = [100, 200];

        $result = $left
                |> iterable_zip($right1, $right2)
                |> iterable_map(\array_product(...))
                |> iterator_to_array(...);

        self::assertSame([
            'one' => 1000,
            'two' => 8000,
        ], $result);
    }
}
