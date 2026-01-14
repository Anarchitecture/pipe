<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\uasort;
use function Anarchitecture\pipe\usort;

final class UasortTest extends TestCase
{
    public function test_returns_a_closure() : void
    {
        $stage = uasort(static fn ($a, $b) => $a <=> $b);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_sorts_by_values_and_preserves_numeric_keys() : void
    {
        $stage = [
            10 => 2,
            2 => 1,
            7 => 3
        ];

        $result = $stage
            |> uasort(static fn ($a, $b) => $a <=> $b);

        self::assertSame([
            2 => 1,
            10 => 2,
            7 => 3
        ], $result);

        self::assertSame([
            10 => 2,
            2 => 1,
            7 => 3
        ], $stage);
    }

    public function test_sorts_by_values_and_preserves_string_keys() : void
    {
        $stage = [
            'b' => 2,
            'c' => 1,
            'a' => 3
        ];

        $result = $stage
            |> uasort(static fn ($a, $b) => $a <=> $b);

        self::assertSame([
            'c' => 1,
            'b' => 2,
            'a' => 3
        ], $result);
    }
}
