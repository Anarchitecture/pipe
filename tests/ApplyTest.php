<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\apply;

final class ApplyTest extends TestCase
{
    public function test_numeric_keys_are_applied_positionally_in_iteration_order() : void {

        $stage = [
            20 => 'b',
            10 => 'a',
            30 => 'c',
        ];

        $result = $stage
            |> apply(static fn (string $a, string $b, string $c) => $a . $b . $c);

        self::assertSame('bac', $result);
    }

    public function test_list_is_applied_positionally() : void {

        $stage = [
            1,
            2
        ];

        $result = $stage
            |> apply(static fn(int $a, int $b) => $a + $b);

        self::assertSame(3, $result);
    }

    public function test_string_keys_are_applied_as_named_arguments() : void {

        $stage = [
            "b" => 2,
            "a" => 1
        ];

        $result = $stage
            |> apply(static fn(int $a, int $b) => $a - $b);

        self::assertSame(-1, $result);

    }

    public function test_mixed_numeric_and_string_keys_throw(): void
    {
        $stage = [
            0 => 'x',
            'b' => 'y'
        ];

        $this->expectException(InvalidArgumentException::class);
        $stage
            |> apply(static fn(string $a, string $b) => $a . $b);
    }

    public function test_empty_array_calls_callback_with_no_arguments() : void {

        $stage = [];

        $result = $stage
            |> apply(static fn(...$args) => $args);

        self::assertSame([], $result);
    }

    public function test_unknown_named_parameter_errors_are_propagated() : void {

        $stage = [
            'a' => '1',
            'b' => '2'
        ];

        $this->expectException(\Error::class);
        $stage
            |> apply(static fn(string $x, string $y) => $x . $y);
    }
}
