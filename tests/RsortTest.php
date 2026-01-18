<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\rsort;

final class RsortTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = rsort();

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_sorts_descending() : void {

        $stage = [3, 1, 2];

        $result = $stage
            |> rsort();

        self::assertSame([3, 2, 1], $result);
    }

    public function test_reindexes_numeric_keys() : void {

        $stage = [
            10 => 'b',
            20 => 'a',
            30 => 'c',
        ];

        $result = $stage
            |> rsort();

        self::assertSame(['c', 'b', 'a'], $result);
    }

    public function test_does_not_mutate_original_array_variable() : void {

        $stage = [2, 1, 3];

        $result = $stage
            |> rsort();

        self::assertSame([2, 1, 3], $stage);
        self::assertSame([3, 2, 1], $result);
    }

    public function test_forwards_sort_flags() : void {

        $stage = ['2', '10', '1'];

        $regular = $stage
            |> rsort();

        $string  = $stage
            |> rsort(\SORT_STRING);

        self::assertSame(['10', '2', '1'], $regular);
        self::assertSame(['2', '10', '1'], $string);
    }

}
