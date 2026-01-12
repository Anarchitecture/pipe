<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\equals;
use function Anarchitecture\pipe\if_else;
use function Anarchitecture\pipe\value;
use function Anarchitecture\pipe\when;

final class IfElseTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = if_else(equals(1), value(2), value(3));

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_applies_then_when_predicate_is_true() : void {

        $stage = "  Hello  ";

        $result = $stage
            |> if_else(is_string(...), trim(...), value("nope"));

        self::assertSame("Hello", $result);
    }

    public function test_applies_else_when_predicate_is_false() : void {

        $stage = 123;

        $result = $stage
            |> if_else(is_string(...), trim(...), value("nope"));

        self::assertSame("nope", $result);
    }

    public function test_predicate_must_return_true_strictly() : void {

        $stage = "x";

        $result = $stage
            |> if_else(value(1), value("then"), value("else"));

        self::assertSame("else", $result);
    }

    public function test_composes_with_when() : void {

        $stage = "Hello";

        $result = $stage
            |> when(
                    is_string(...),
                    if_else(equals("Hello"), value("bye"), value("unknown"))
                );

        self::assertSame("bye", $result);
    }

    public function test_passes_value_into_branches() : void {

        $stage = "x";

        $result_then = $stage |>
            if_else(
                equals("x"),
                fn(string $v) => $v . "1",
                fn(string $v) => $v . "2",
            );

        $result_else = $stage |>
            if_else(
                equals("y"),
                fn(string $v) => $v . "1",
                fn(string $v) => $v . "2",
            );

        self::assertSame("x1", $result_then);
        self::assertSame("x2", $result_else);
    }

}
