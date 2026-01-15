<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use ArgumentCountError;
use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_reduce;

final class ArrayReduceTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = array_reduce(static fn ($carry, $v) => $carry);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_reduces_values_left_to_right() : void {

        $stage = ["a", "b", "c"];

        $result = $stage
            |> array_reduce(static fn (string $carry, string $v) : string => $carry . $v, "");

        self::assertSame("abc", $result);
    }

    public function test_empty_array_returns_initial_and_does_not_call_reducer() : void {

        $calls = 0;

        $callback = static function ($carry, $v) use (&$calls) {
            $calls++;
            return $carry;
        };

        $result = []
            |> array_reduce($callback, 123);

        self::assertSame(123, $result);
        self::assertSame(0, $calls);
    }

    public function test_empty_array_returns_null_when_initial_is_omitted() : void {

        $result = []
            |> array_reduce(static fn ($carry, $v) => $carry);

        self::assertNull($result);
    }

    public function test_does_not_mutate_the_input_array() : void {

        $stage = [1, 2, 3];
        $before = $stage;

        $result = $stage
            |> array_reduce(static fn (?int $carry, int $v) : int => ($carry ?? 0) + $v, 0);

        self::assertSame(6, $result);
        self::assertSame($before, $stage);
    }

    public function test_throws_when_reducer_expects_a_key_but_only_carry_and_value_are_provided() : void {

        $this->expectException(ArgumentCountError::class);

        $stage = ['a' => 10, 'b' => 20];

        $stage
            |> array_reduce(fn ($carry, $v, $k) => $carry, 0);
    }
}
