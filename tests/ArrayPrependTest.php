<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_prepend;

final class ArrayPrependTest extends TestCase
{
    public function test_returns_a_closure(): void
    {
        $stage = array_prepend(1);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_prepends_values(): void
    {
        $stage = [3, 4];

        $result = $stage
            |> array_prepend(1, 2);

        self::assertSame([1, 2, 3, 4], $result);
    }

    public function test_prepends_array_as_single_value(): void
    {
        $stage = [
            [1, 0],
            [2, 0],
        ];

        $result = $stage
            |> array_prepend([0, 0]);

        self::assertSame([
            [0, 0],
            [1, 0],
            [2, 0],
        ], $result);
    }

    public function test_reindexes_numeric_keys_and_preserves_string_keys(): void
    {
        $stage = [
            2   => 'a',
            'x' => 'b',
            10  => 'c',
        ];

        $result = $stage
            |> array_prepend('start');

        self::assertSame([
            0   => 'start',
            1   => 'a',
            'x' => 'b',
            2   => 'c',
        ], $result);
    }

    public function test_no_values_leaves_array_unchanged(): void
    {
        $stage = [
            'a' => 1,
            'b' => 2,
        ];

        $result = $stage
            |> array_prepend();

        self::assertSame($stage, $result);
    }

    public function test_does_not_modify_original_array(): void
    {
        $stage = [2, 3];

        $result = $stage
            |> array_prepend(1);

        self::assertSame([2, 3], $stage);
        self::assertSame([1, 2, 3], $result);
    }
}
