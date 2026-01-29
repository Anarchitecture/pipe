<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\unless;
use function Anarchitecture\pipe\value;

final class UnlessTest extends TestCase
{
    public function test_applies_callback_when_predicate_false(): void
    {

        $stage = "  hello  ";

        $result = $stage
            |> unless(is_int(...), trim(...));

        self::assertSame("hello", $result);
    }

    public function test_returns_input_unchanged_when_predicate_true(): void
    {

        $stage = (object) ['x' => 1];

        $result = $stage
            |> unless(is_object(...), get_object_vars(...));

        self::assertSame($stage, $result);
    }

    public function test_does_not_call_callback_when_predicate_true(): void
    {

        $stage = 123;

        $called = false;
        $call = function ($_) use (&$called): string {
            $called = true;
            return "should-not-happen";
        };

        $result = $stage
            |> unless(is_int(...), $call);

        self::assertFalse($called);
        self::assertSame(123, $result);
    }

    public function test_accepts_callable_strings(): void
    {

        $stage = "  hi  ";

        $result = $stage
            |> unless(value(false), 'trim');

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
            |> unless($call, trim(...))
            |> unless($call, strtoupper(...));

        self::assertSame("x ", $result);
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
                |> unless(is_int(...), $call)
                |> unless(is_int(...), trim(...))
                |> unless(is_int(...), $call)
                |> unless(is_int(...), strtoupper(...))
                |> unless(is_string(...), $call);

        self::assertSame([
            '  hi  ',
            'hi',
        ], $seen);

        self::assertSame("HI", $result);
    }

    public function test_predicate_must_return_strict_not_true_to_apply_callback(): void
    {

        $stage = "  hi  ";

        $result = $stage
            |> unless(fn($_) => 0, 'trim');

        self::assertSame("hi", $result);

        $result = $stage
            |> unless(fn($_) => null, 'trim');

        self::assertSame("hi", $result);

        $stage = "  hi  ";

        $result = $stage
            |> unless(fn($_) => 1, 'trim');

        self::assertSame("hi", $result);

        $result = $stage
            |> unless(fn($_) => "yes", 'trim');

        self::assertSame("hi", $result);

        $result = $stage
            |> unless(fn($_) => true, 'trim');

        self::assertSame("  hi  ", $result);


        $result = $stage
            |> unless(fn($_) => false, 'trim');

        self::assertSame("hi", $result);
    }

}
