<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_append;

final class ArrayAppendTest extends TestCase
{
    public function test_returns_a_closure(): void
    {
        $stage = array_append(1);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_appends_values(): void
    {
        $stage = [1, 2];

        $result = $stage
            |> array_append(3, 4);

        self::assertSame([1, 2, 3, 4], $result);
    }

    public function test_appends_array_as_single_value(): void
    {
        $stage = [
            [0, 0],
            [1, 0],
        ];

        $result = $stage
            |> array_append([2, 0]);

        self::assertSame([
            [0, 0],
            [1, 0],
            [2, 0],
        ], $result);
    }

    public function test_preserves_existing_keys(): void
    {
        $stage = [
            2   => 'a',
            'x' => 'b',
            10  => 'c',
        ];

        $result = $stage
            |> array_append('d');

        self::assertSame([
            2   => 'a',
            'x' => 'b',
            10  => 'c',
            11  => 'd',
        ], $result);
    }

    public function test_no_values_leaves_array_unchanged(): void
    {
        $stage = [
            'a' => 1,
            'b' => 2,
        ];

        $result = $stage
            |> array_append();

        self::assertSame($stage, $result);
    }

    public function test_does_not_modify_original_array(): void
    {
        $stage = [1, 2];

        $result = $stage
            |> array_append(3);

        self::assertSame([1, 2], $stage);
        self::assertSame([1, 2, 3], $result);
    }
}
