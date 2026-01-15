<?php


declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use ArgumentCountError;
use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_reduce_until;

final class ArrayReduceUntilTest extends TestCase
{

    public function test_returns_a_closure() : void {

        $stage = array_reduce_until(
            static fn(int $carry, int $v, int|string $k): int => $carry + $v,
            static fn(int $carry, int $v, int|string $k): bool => false,
            0
        );

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_reduces_until_predicate_matches_and_returns_carry_key_value() : void {

        $stage = ['a' => 2, 'b' => 3, 'c' => 4];

        $seen = [];

        $reducer = static function (int $carry, int $v, int|string $k) use (&$seen): int {
            $carry = $carry + $v;
            $seen[] = ['reduce', $k, $v, $carry];
            return $carry;
        };

        $predicate = static function (int $carry, int $v, int|string $k) use (&$seen): bool {
            $seen[] = ['until', $k, $v, $carry];
            return $carry >= 5;
        };

        [$carry, $key, $value] = $stage
            |> array_reduce_until($reducer, $predicate,0);

        self::assertSame(5, $carry);
        self::assertSame('b', $key);
        self::assertSame(3, $value);

        self::assertSame([
            ['reduce', 'a', 2, 2],
            ['until', 'a', 2, 2],
            ['reduce', 'b', 3, 5],
            ['until', 'b', 3, 5],
        ], $seen);
    }

    public function test_when_never_triggered_returns_carry_and_null_key_value() : void {

        $input = ['a' => 2, 'b' => 3];

        [$carry, $key, $value] = $input
            |> array_reduce_until(
                static fn(int $carry, int $v, int|string $k): int => $carry + $v,
                static fn(int $carry, int $v, int|string $k): bool => false,
                10
            );

        self::assertSame(15, $carry);
        self::assertNull($key);
        self::assertNull($value);
    }

    public function test_empty_array_returns_initial_and_does_not_call_reducer_or_until() : void {

        $reduce_calls = 0;
        $until_calls = 0;

        $predicate = static function ($carry, $v, $k) use (&$until_calls): bool {
            $until_calls++;
            return true;
        };

        $reducer = static function ($carry, $v, $k) use (&$reduce_calls) {
            $reduce_calls++;
            return $carry;
        };

        [$carry, $key, $value] = []
            |> array_reduce_until($reducer, $predicate,123);

        self::assertSame(123, $carry);
        self::assertNull($key);
        self::assertNull($value);
        self::assertSame(0, $reduce_calls);
        self::assertSame(0, $until_calls);
    }

    public function test_does_not_mutate_the_input_array() : void {

        $stage = ['a' => 2, 'b' => 3, 'c' => 4];
        $before = $stage;

        $result = $stage
            |> array_reduce_until(
                static fn(int $carry, int $v, int|string $k): int => $carry + $v,
                static fn(int $carry, int $v, int|string $k): bool => $carry >= 5,
                0,
            );

        self::assertSame([5, 'b', 3], $result);
        self::assertSame($before, $stage);
    }

    public function test_throws_when_reducer_expects_too_many_arguments() : void {

        $this->expectException(ArgumentCountError::class);

        $stage = [1, 2, 3];

        $stage |> array_reduce_until(
            fn ($carry, $v, $k, $extra) => $carry,
            fn ($carry, $v, $k): bool => false,
            0,
        );
    }

    public function test_throws_when_until_expects_too_many_arguments() : void {

        $this->expectException(ArgumentCountError::class);

        $input = [1, 2, 3];

        $input |> array_reduce_until(
            fn ($carry, $v, $k, $extra) => $carry,
            fn ($carry, $v, $k): bool => false,
            0,
        );
    }
}
