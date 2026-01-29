<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\increment;

final class IncrementTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = increment();

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_increments_by_one_by_default(): void
    {

        $stage = 41;

        $result = $stage |> increment();

        self::assertSame(42, $result);
    }

    public function test_increments_by_custom_amount(): void
    {

        $stage = 10;

        $result = $stage
            |> increment(5);

        self::assertSame(15, $result);
    }

    public function test_supports_negative_increment(): void
    {

        $stage = 10;

        $result = $stage
            |> increment(-3);

        self::assertSame(7, $result);
    }

    public function test_supports_floats(): void
    {

        $stage = 1.5;

        $result = $stage
            |> increment(0.25);

        self::assertSame(1.75, $result);
    }
}
