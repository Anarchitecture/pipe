<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\sort;

final class SortTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = sort();

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_sorts_ascending() : void {

        $stage = [3, 1, 2];

        $result = $stage
            |> sort();

        self::assertSame([1, 2, 3], $result);
    }

    public function test_reindexes_numeric_keys() : void {

        $stage = [
            10 => 'b',
            20 => 'a',
            30 => 'c',
        ];

        $result = $stage
            |> sort();

        self::assertSame(['a', 'b', 'c'], $result);
    }

    public function test_does_not_mutate_original_array_variable() : void {

        $stage = [2, 1, 3];

        $result = $stage
            |> sort();

        self::assertSame([2, 1, 3], $stage);
        self::assertSame([1, 2, 3], $result);
    }

    public function test_forwards_sort_flags() : void {

        $stage = ['2', '10', '1'];

        $regular = $stage
            |> sort();

        $string  = $stage
            |> sort(\SORT_STRING);

        self::assertSame(['1', '2', '10'], $regular);
        self::assertSame(['1', '10', '2'], $string);
    }
}
