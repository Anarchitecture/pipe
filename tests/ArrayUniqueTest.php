<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_unique;

final class ArrayUniqueTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = array_unique();

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_removes_duplicates_and_preserves_first_occurrence_keys() : void {

        $stage = [
            10 => 'a',
            20 => 'b',
            30 => 'a',
            'x' => 'b',
            'y' => 'c',
        ];

        $result = $stage
            |> array_unique();

        self::assertSame([
            10  => 'a',
            20  => 'b',
            'y' => 'c',
        ], $result);
    }

    public function test_default_flags_sort_string_means_string_cast_comparison() : void {


        $stage = [
            'a' => 1,
            'b' => '1',
            'c' => 2,
        ];

        $result = $stage
            |> array_unique();

        self::assertSame([
            'a' => 1,
            'c' => 2,
        ], $result);
    }

    public function test_can_use_numeric_comparison_when_flags_sort_numeric() : void {

        $stage = [
            'a' => '01',
            'b' => '1',
            'c' => 1,
            'd' => 2,
        ];

        $result = $stage
            |> array_unique(\SORT_NUMERIC);

        self::assertSame([
            'a' => '01',
            'd' => 2,
        ], $result);
    }

    public function test_empty_array_returns_empty() : void {

        $result = []
            |> array_unique();

        self::assertSame([], $result);
    }

    public function test_does_not_mutate_the_input_array() : void {

        $stage = [1, 1, 2, 2, 3];
        $before = $stage;

        $result = $stage
            |> array_unique();

        self::assertSame([0 => 1, 2 => 2, 4 => 3], $result);
        self::assertSame($before, $stage);
    }

    public function test_throws_value_error_for_invalid_flags() : void {

        $this->expectException(\ValueError::class);

        $stage = [1, 1, 2];

        $stage
            |> array_unique(123);
    }
}
