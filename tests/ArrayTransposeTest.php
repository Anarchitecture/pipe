<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_transpose;

final class ArrayTransposeTest extends TestCase
{
    public function test_returns_a_closure(): void
    {
        self::assertInstanceOf(Closure::class, array_transpose());
    }

    public function test_transposes_a_matrix(): void
    {

        $stage = [
            [1, 2, 3],
            [4, 5, 6],
        ];

        $result = $stage
            |> array_transpose();

        self::assertSame([
            [1, 4],
            [2, 5],
            [3, 6],
        ], $result);
    }

    public function test_empty_input_returns_empty_array(): void
    {

        $stage = [];

        $result = $stage
            |> array_transpose();

        self::assertSame([], $result);
    }

    public function test_single_row_becomes_column_vectors(): void
    {

        $stage = [
            [10, 20, 30],
        ];

        $result = $stage
            |> array_transpose();

        self::assertSame([
            [10],
            [20],
            [30],
        ], $result);
    }

    public function test_ragged_rows_are_padded_with_null(): void
    {

        $stage = [
            [1, 2],
            [3],
        ];

        $result = $stage
            |> array_transpose();

        self::assertSame([
            [1, 3],
            [2, null],
        ], $result);
    }

    public function test_preserves_row_and_column_keys(): void
    {

        $stage = [
            'r1' => ['a' => 1, 'b' => 2],
            'r2' => ['a' => 3, 'c' => 4],
        ];

        $result = $stage
            |> array_transpose();

        self::assertSame([
            'a' => ['r1' => 1,    'r2' => 3],
            'b' => ['r1' => 2,    'r2' => null],
            'c' => ['r1' => null, 'r2' => 4],
        ], $result);
    }

    public function test_single_row_preserves_column_keys_and_maps_to_row_keys(): void
    {

        $stage = [
            'r1' => ['a' => 10, 'b' => 20, 'c' => 30],
        ];

        $result = $stage
            |> array_transpose();

        self::assertSame([
            'a' => ['r1' => 10],
            'b' => ['r1' => 20],
            'c' => ['r1' => 30],
        ], $result);
    }

    public function test_ragged_rows_are_padded_with_null_and_keys_preserved(): void
    {

        $stage = [
            10 => [0 => 1, 1 => 2],
            20 => [0 => 3],
        ];

        $result = $stage
            |> array_transpose();

        self::assertSame([
            0 => [10 => 1, 20 => 3],
            1 => [10 => 2, 20 => null],
        ], $result);
    }

    public function test_column_key_order_is_first_seen_order(): void
    {

        $stage = [
            'r1' => ['b' => 2, 'a' => 1],
            'r2' => ['c' => 3, 'b' => 20],
        ];

        $result = $stage
            |> array_transpose()
            |> array_keys(...);

        self::assertSame(['b', 'a', 'c'], $result);
    }
}
