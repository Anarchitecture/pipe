<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_flatten;

final class ArrayFlattenTest extends TestCase
{
    public function test_flattens_one_level(): void
    {

        $stage = [
            [1, 2],
            [3],
            [4, 5],
        ];

        $result = $stage
            |> array_flatten(...);

        self::assertSame([1, 2, 3, 4, 5], $result);
    }

    public function test_empty_outer_array_returns_empty_array(): void
    {

        $result = []
            |> array_flatten(...);

        self::assertSame([], $result);
    }

    public function test_reindexes_numeric_keys(): void
    {

        $stage = [
            [10 => 'a'],
            [20 => 'b'],
        ];

        $result = $stage
            |> array_flatten(...);

        self::assertSame([0 => 'a', 1 => 'b'], $result);
    }

    public function test_preserves_string_keys(): void
    {

        $stage = [
            ['a' => 1, 'b' => 2],
            ['c' => 3],
        ];

        $result = $stage
            |> array_flatten(...);

        self::assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ], $result);
    }

    public function test_does_not_mutate_the_original_array(): void
    {

        $stage = [[1], [2, 3]];
        $before = $stage;

        $result = $stage
                |> array_flatten(...);

        self::assertSame([1, 2, 3], $result);
        self::assertSame($before, $stage);
    }

    public function test_throws_type_error_when_any_element_is_not_an_array(): void
    {

        $this->expectException(\TypeError::class);

        $stage = [
            [1, 2],
            'not-an-array',
        ];

        /** @phpstan-ignore-next-line */
        $stage |> array_flatten(...);
    }

    public function test_throws_type_error_when_any_element_is_null(): void
    {

        $this->expectException(\TypeError::class);

        $stage = [
            [1, 2],
            null,
        ];

        /** @phpstan-ignore-next-line */
        $stage |> array_flatten(...);
    }

    public function test_when_string_keys_clash_later_values_win(): void
    {

        $stage = [
            ['a' => 1, 'b' => 2],
            ['a' => 99, 'c' => 3],
            ['b' => 42],
        ];

        $result = $stage
            |> array_flatten(...);

        self::assertSame([
            'a' => 99,
            'b' => 42,
            'c' => 3,
        ], $result);
    }

    public function test_mixed_keys_preserve_string_keys_and_reindex_numeric_keys(): void
    {

        $stage = [
            ['a' => 1, 10 => 'x'],
            [20 => 'y', 'b' => 2],
            ['a' => 99, 30 => 'z'],
        ];

        $result = $stage
            |> array_flatten(...);

        self::assertSame([
            'a' => 99,
            0   => 'x',
            1   => 'y',
            'b' => 2,
            2   => 'z',
        ], $result);
    }


}
