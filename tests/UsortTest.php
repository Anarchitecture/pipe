<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\usort;

final class UsortTest extends TestCase
{
    public function test_returns_a_closure(): void
    {
        $stage = usort(static fn($a, $b) => $a <=> $b);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_sorts_by_values_and_reindexes_numeric_keys(): void
    {

        $stage = [
            10 => 2,
            2 => 1,
            7 => 3,
        ];

        $result = $stage
            |> usort(static fn($a, $b) => $a <=> $b);

        self::assertSame([
            0 => 1,
            1 => 2,
            2 => 3,
        ], $result);

        self::assertSame([
            10 => 2,
            2 => 1,
            7 => 3,
        ], $stage);
    }

    public function test_sorts_by_values_and_reindexes_string_keys(): void
    {

        $stage = [
            'b' => 2,
            'c' => 1,
            'a' => 3,
        ];

        $result = $stage
            |> usort(static fn($a, $b) => $a <=> $b);

        self::assertSame([
            0 => 1,
            1 => 2,
            2 => 3,
        ], $result);
    }

    public function test_uses_comparator_to_determine_order(): void
    {

        $stage = [1, 3, 2];

        $result = $stage
            |> usort(static fn(int $a, int $b): int => $b <=> $a);

        self::assertSame([3, 2, 1], $result);
    }

}
