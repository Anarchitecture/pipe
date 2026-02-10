<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use Generator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_reduce_until;

final class IterableReduceUntilTest extends TestCase
{
    public function test_returns_a_closure(): void
    {
        $stage = iterable_reduce_until(
            static fn(int $carry, int $v, int|string $k): int => $carry + $v,
            static fn(int $carry, int $v, int|string $k): bool => false,
            0
        );

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_reduces_until_predicate_matches_and_short_circuits(): void
    {
        $seen = [];

        $iterable = (static function () use (&$seen): Generator {
            $seen[] = ['yield', 'a', 2];
            yield 'a' => 2;

            $seen[] = ['yield', 'b', 3];
            yield 'b' => 3;

            $seen[] = ['yield', 'c', 4];
            yield 'c' => 4;
        })();

        $reducer = static function (int $carry, int $v, int|string $k) use (&$seen): int {
            $carry = $carry + $v;
            $seen[] = ['reduce', $k, $v, $carry];
            return $carry;
        };

        $predicate = static function (int $carry, int $v, int|string $k) use (&$seen): bool {
            $seen[] = ['until', $k, $v, $carry];
            return $carry >= 5;
        };

        [$carry, $key, $value] = $iterable
            |> iterable_reduce_until($reducer, $predicate, 0);

        self::assertSame(5, $carry);
        self::assertSame('b', $key);
        self::assertSame(3, $value);

        self::assertSame([
            ['yield', 'a', 2],
            ['reduce', 'a', 2, 2],
            ['until', 'a', 2, 2],
            ['yield', 'b', 3],
            ['reduce', 'b', 3, 5],
            ['until', 'b', 3, 5],
        ], $seen);
    }

    public function test_when_never_triggered_returns_carry_and_null_key_value(): void
    {
        $input = ['a' => 2, 'b' => 3];

        $reducer = static fn(int $carry, int $v, int|string $k): int => $carry + $v;
        $predicate = static fn(int $carry, int $v, int|string $k): bool => false;

        [$carry, $key, $value] = $input
            |> iterable_reduce_until($reducer, $predicate, 10);

        self::assertSame(15, $carry);
        self::assertNull($key);
        self::assertNull($value);
    }

    public function test_empty_iterable_returns_initial_and_does_not_call_reducer_or_until(): void
    {
        $reduceCalls = 0;
        $untilCalls = 0;

        $predicate = static function ($carry, $v, $k) use (&$untilCalls): bool {
            $untilCalls++;
            return true;
        };

        $reducer = static function ($carry, $v, $k) use (&$reduceCalls) {
            $reduceCalls++;
            return $carry;
        };

        $empty = (static function (): Generator {
            /** @phpstan-ignore-next-line  */
            if (false) {
                yield 1;
            }
            return;
        })();

        [$carry, $key, $value] = $empty
            |> iterable_reduce_until($reducer, $predicate, 123);

        self::assertSame(123, $carry);
        self::assertNull($key);
        self::assertNull($value);
        self::assertSame(0, $reduceCalls);
        self::assertSame(0, $untilCalls);
    }
}
