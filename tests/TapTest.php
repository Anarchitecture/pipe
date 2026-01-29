<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\tap;

final class TapTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = tap(static fn(mixed $_) => null);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_calls_callback_and_returns_original_value(): void
    {

        $seen = null;

        $value = ['a' => 1];

        $callback = static function (mixed $v) use (&$seen): void {
            $seen = $v;
        };

        $result = $value
            |> tap($callback);

        self::assertSame($value, $result);
        self::assertSame($value, $seen);
    }

    public function test_can_be_used_for_side_effects_without_changing_type(): void
    {

        $calls = 0;

        $callback = static function (int $_) use (&$calls): void {
            $calls++;
        };

        $result = 42
            |> tap($callback)
            |> tap($callback);

        self::assertSame(42, $result);
        self::assertSame(2, $calls);
    }
}
