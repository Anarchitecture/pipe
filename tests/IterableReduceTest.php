<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use Generator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_reduce;

final class IterableReduceTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = iterable_reduce(static fn (mixed $carry, mixed $value, mixed $key) => $carry, null);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_reduces_iterable_with_initial_value() : void {

        $stage = [1, 2, 3, 4];

        $reducer = static fn(int $carry, int $value): int => $carry + $value;

        $result = $stage
            |> iterable_reduce($reducer, 0);

        self::assertSame(10, $result);
    }

    public function test_reducer_receives_key() : void {

        $stage = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
        ];

        $reducer = static fn(string $carry, int $_v, string $key): string => $carry . $key;

        $result = $stage
            |> iterable_reduce($reducer, '');

        self::assertSame('abc', $result);
    }

    public function test_empty_iterable_returns_initial_and_does_not_call_reducer() : void {

        $calls = 0;

        $reducer = static function (mixed $carry) use (&$calls) : mixed {
            $calls++;
            return $carry;
        };

        $result = []
            |> iterable_reduce($reducer, 123);

        self::assertSame(123, $result);
        self::assertSame(0, $calls);
    }

    public function test_works_with_generators() : void {

        $stage = (function () : Generator {
            yield 'x' => 2;
            yield 'y' => 3;
            yield 'z' => 5;
        })();

        $reducer = static fn(int $carry, int $value): int => $carry * $value;

        $result = $stage
            |> iterable_reduce($reducer, 1);

        self::assertSame(30, $result);
    }

    public function test_initial_defaults_to_null_when_omitted() : void {

        $stage = [1, 2, 3];

        $reducer = static function (?array $carry, int $value): array {
            $carry ??= [];
            $carry[] = $value;
            return $carry;
        };

        $result = $stage
            |> iterable_reduce($reducer);

        self::assertSame([1, 2, 3], $result);
    }

    public function test_reducer_is_called_with_three_arguments() : void {

        $stage = [
            'a' => 1,
            'b' => 2,
        ];

        $reducer = static function (string $carry, int $value, string $key) : string {
            return $carry . $key . '=' . $value . ';';
        };

        $result = $stage
            |> iterable_reduce($reducer, '');

        self::assertSame('a=1;b=2;', $result);
    }
}
