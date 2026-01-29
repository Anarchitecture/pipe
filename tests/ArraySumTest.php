<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_sum;

final class ParraySumTest extends TestCase
{
    public function test_returns_a_closure(): void
    {
        $stage = array_sum(static fn($x) => $x);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_sums_identity_mapping(): void
    {
        $stage = [1, 2, 3];

        $callback = static fn(int $x): int => $x;

        $result = $stage
            |> array_sum($callback);

        self::assertSame(6, $result);
    }

    public function test_maps_then_sums(): void
    {

        $stage = [1, 2, 3];

        $callback = static fn(int $x): int => $x * $x;

        $result = $stage
            |> array_sum($callback);

        self::assertSame(14, $result);
    }

    public function test_empty_array_returns_zero(): void
    {

        $stage = [];

        $callback = static fn(int $x): int => $x;

        $result = $stage
            |> array_sum($callback);

        self::assertSame(0, $result);
    }

    public function test_supports_float_results(): void
    {
        $stage = [1, 2];

        $callback = static fn(int $x): float => $x / 2;

        $result = $stage
            |> array_sum($callback);

        self::assertSame(1.5, $result);
    }
}
