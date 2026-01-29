<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use Generator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\collect;
use function Anarchitecture\pipe\iterable_map;

final class IterableMapTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = iterable_map(static fn(mixed $v): mixed => $v);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_maps_values_and_preserves_keys(): void
    {

        $stage = [
            10  => 1,
            20  => 2,
            'x' => 3,
        ];

        $result = $stage
            |> iterable_map(static fn(int $v): int => $v * 10)
            |> collect(...);

        self::assertSame([
            10  => 10,
            20  => 20,
            'x' => 30,
        ], $result);
    }

    public function test_preserves_sparse_numeric_keys(): void
    {

        $stage = [
            2  => 'a',
            10 => 'b',
            11 => 'c',
        ];

        $result = $stage
            |> iterable_map(\strtoupper(...))
            |> collect(...);

        self::assertSame([
            2  => 'A',
            10 => 'B',
            11 => 'C',
        ], $result);
    }

    public function test_works_with_generators_and_preserves_keys(): void
    {

        $stage = (function (): Generator {
            yield 'a' => 1;
            yield 'b' => 2;
            yield 'c' => 3;
        })();

        $result = $stage
            |> iterable_map(static fn(int $v): int => $v + 1)
            |> collect(...);

        self::assertSame([
            'a' => 2,
            'b' => 3,
            'c' => 4,
        ], $result);
    }

    public function test_is_lazy_and_does_not_call_mapper_until_iterated(): void
    {

        $calls = 0;

        $mapper = static function (mixed $v) use (&$calls): mixed {
            $calls++;
            return $v;
        };

        $mapped = [1, 2, 3]
            |> iterable_map($mapper);

        self::assertSame(0, $calls);

        $result = $mapped |> collect(...);

        self::assertSame([1, 2, 3], $result);
        self::assertSame(3, $calls);
    }

    public function test_empty_iterable_yields_empty_and_does_not_call_mapper(): void
    {

        $calls = 0;

        $mapper = static function (mixed $v) use (&$calls): mixed {
            $calls++;
            return $v;
        };

        $result = []
            |> iterable_map($mapper)
            |> collect(...);

        self::assertSame([], $result);
        self::assertSame(0, $calls);
    }

}
