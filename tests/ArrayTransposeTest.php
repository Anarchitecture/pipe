<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_transpose;

final class ArrayTransposeTest extends TestCase {

    public function test_returns_a_closure() : void {
        self::assertInstanceOf(Closure::class, array_transpose());
    }

    public function test_transposes_a_matrix() : void {

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

    public function test_empty_input_returns_empty_array() : void {

        $stage = [];

        $result = $stage
            |> array_transpose();

        self::assertSame([], $result);
    }

    public function test_single_row_becomes_column_vectors() : void {

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

    public function test_ragged_rows_are_padded_with_null() : void {

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
}
