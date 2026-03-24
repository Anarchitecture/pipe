<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use Generator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\collect;
use function Anarchitecture\pipe\iterable_scan;

final class IterableScanTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = iterable_scan(static fn(mixed $state, mixed $value): mixed => $state, null);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_yields_intermediate_state_for_each_iteration(): void
    {

        $stage = [1, 2, 3, 4];

        $callback = static fn(int $state, int $value): int => $state + $value;

        $result = $stage
            |> iterable_scan($callback, 0)
            |> collect(...);

        self::assertSame([1, 3, 6, 10], $result);
    }

    public function test_preserves_keys(): void
    {

        $stage = [
            10  => 1,
            20  => 2,
            'x' => 3,
        ];

        $callback = static fn(int $state, int $value): int => $state + $value;

        $result = $stage
            |> iterable_scan($callback, 0)
            |> collect(...);

        self::assertSame([
            10  => 1,
            20  => 3,
            'x' => 6,
        ], $result);
    }

    public function test_preserves_sparse_numeric_keys(): void
    {

        $stage = [
            2  => 5,
            10 => 7,
            11 => 11,
        ];

        $callback = static fn(int $state, int $value): int => $state + $value;

        $result = $stage
            |> iterable_scan($callback, 0)
            |> collect(...);

        self::assertSame([
            2  => 5,
            10 => 12,
            11 => 23,
        ], $result);
    }

    public function test_callback_receives_key(): void
    {

        $stage = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
        ];

        $callback = static fn(string $state, int $_value, string $key): string => $state . $key;

        $result = $stage
            |> iterable_scan($callback, '')
            |> collect(...);

        self::assertSame([
            'a' => 'a',
            'b' => 'ab',
            'c' => 'abc',
        ], $result);
    }

    public function test_works_with_generators_and_preserves_keys(): void
    {

        $stage = (function (): Generator {
            yield 'x' => 2;
            yield 'y' => 3;
            yield 'z' => 5;
        })();

        $callback = static fn(int $state, int $value): int => $state * $value;

        $result = $stage
            |> iterable_scan($callback, 1)
            |> collect(...);

        self::assertSame([
            'x' => 2,
            'y' => 6,
            'z' => 30,
        ], $result);
    }

    public function test_is_lazy_and_does_not_call_callback_until_iterated(): void
    {

        $calls = 0;

        $scanner = static function (int $state, int $value) use (&$calls): int {
            $calls++;
            return $state + $value;
        };

        $scanned = [1, 2, 3]
            |> iterable_scan($scanner, 0);

        self::assertSame(0, $calls);

        $result = $scanned
            |> collect(...);

        self::assertSame([1, 3, 6], $result);
        self::assertSame(3, $calls);
    }

    public function test_empty_iterable_yields_empty_and_does_not_call_callback(): void
    {

        $calls = 0;

        $scanner = static function (mixed $state) use (&$calls): mixed {
            $calls++;
            return $state;
        };

        $result = []
            |> iterable_scan($scanner, 123)
            |> collect(...);

        self::assertSame([], $result);
        self::assertSame(0, $calls);
    }

    public function test_initial_defaults_to_null_when_omitted(): void
    {

        $stage = [1, 2, 3];

        $scanner = static function (?array $state, int $value): array {
            $state ??= [];
            $state[] = $value;
            return $state;
        };

        $result = $stage
            |> iterable_scan($scanner)
            |> collect(...);

        self::assertSame([
            [1],
            [1, 2],
            [1, 2, 3],
        ], $result);
    }
}
