<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\zip_map;

final class ZipMapTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = zip_map(static fn() => null);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_maps_over_multiple_arrays_with_callback(): void
    {

        $stage = [
            [1, 2, 3],
            [10, 20, 30],
        ];

        $result = $stage
            |> zip_map(static fn(int $a, int $b): int => $a + $b);

        self::assertSame([11, 22, 33], $result);
    }

    public function test_zips_when_callback_is_null(): void
    {
        $stage = [
            [1, 2, 3],
            [10, 20, 30],
        ];

        $result = $stage
            |> zip_map(null);

        self::assertSame([
            [1, 10],
            [2, 20],
            [3, 30],
        ], $result);
    }

    public function test_uneven_lengths_are_padded_with_null(): void
    {

        $stage = [
            [1, 2, 3],
            [10],
        ];

        $result = $stage
            |> zip_map(null);

        self::assertSame([
            [1, 10],
            [2, null],
            [3, null],
        ], $result);
    }

    public function test_empty_input_list_returns_empty_array(): void
    {

        $stage = [];

        $result = $stage
            |> zip_map(null);

        self::assertSame([], $result);
    }

    public function test_reindexes_keys(): void
    {

        $stage = [
            ['a' => 1, 'b' => 2],
            ['a' => 10, 'b' => 20],
        ];

        $result = $stage
            |> zip_map(null);

        self::assertSame([
            0 => [1, 10],
            1 => [2, 20],
        ], $result);
    }
}
