<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\when;

final class WhenTest extends TestCase
{
    public function test_applies_callback_when_predicate_true(): void
    {

        $stage = "  hello  ";

        $result = $stage
            |> when(is_string(...), trim(...));

        self::assertSame("hello", $result);
    }

    public function test_returns_input_unchanged_when_predicate_false(): void
    {

        $stage = (object) ['x' => 1];

        $result = $stage
            |> when(is_string(...), trim(...));

        self::assertSame($stage, $result);
    }

    public function test_does_not_call_callback_when_predicate_false(): void
    {

        $stage = 123;

        $called = false;
        $call = function ($_) use (&$called): string {
            $called = true;
            return "should-not-happen";
        };

        $result = $stage
                |> when(is_string(...), $call);

        self::assertFalse($called);
        self::assertSame(123, $result);
    }

    public function test_accepts_callable_strings(): void
    {

        $stage = "  hi  ";

        $result = $stage
                |> when('is_string', 'trim');

        self::assertSame('hi', $result);
    }

    public function test_predicate_is_called_once_per_invocation(): void
    {

        $stage = "x ";

        $calls = 0;
        $call = function ($_) use (&$calls): bool {
            $calls++;
            return true;
        };

        $result = $stage
            |> when($call, trim(...))
            |> when($call, strtoupper(...));

        self::assertSame("X", $result);
        self::assertSame(2, $calls);
    }

    public function test_callback_receives_original_value(): void
    {

        $stage = "  hi  ";

        $seen = [];

        $call = function ($x) use (&$seen) {
            $seen[] = $x;
            return $x;
        };

        $result = $stage
            |> when(is_string(...), $call)
            |> when(is_string(...), trim(...))
            |> when(is_string(...), $call)
            |> when(is_string(...), strtoupper(...))
            |> when(is_int(...), $call);

        self::assertSame([
            '  hi  ',
            'hi',
        ], $seen);

        self::assertSame("HI", $result);
    }
}
