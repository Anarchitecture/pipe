<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use Generator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\collect;
use function Anarchitecture\pipe\iterable_duplicates;
use function Anarchitecture\pipe\iterable_first;

final class IterableDuplicatesTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = iterable_duplicates();

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_yields_duplicates_and_preserves_keys(): void
    {

        $stage = [
            'a' => 1,
            'b' => 2,
            'c' => 1,
            'd' => 2,
        ];

        $result = $stage
            |> iterable_duplicates()
            |> collect(...);

        self::assertSame([
            'c' => 1,
            'd' => 2,
        ], $result);
    }

    public function test_yields_every_occurrence_after_the_first(): void
    {

        $result = [1, 1, 1]
            |> iterable_duplicates()
            |> collect(...);

        self::assertSame([
            1 => 1,
            2 => 1,
        ], $result);
    }

    public function test_compares_values_strictly(): void
    {

        $result = [1, '1', 1, '1']
            |> iterable_duplicates()
            |> collect(...);

        self::assertSame([
            2 => 1,
            3 => '1',
        ], $result);
    }

    public function test_supports_array_values(): void
    {

        $result = [
            [1, 2],
            [2, 3],
            [1, 2],
            ['1', 2],
        ]
            |> iterable_duplicates()
            |> collect(...);

        self::assertSame([
            2 => [1, 2],
        ], $result);
    }

    public function test_compares_objects_by_identity(): void
    {

        $a = (object) ['value' => 1];
        $b = (object) ['value' => 1];

        $result = [$a, $b, $a]
            |> iterable_duplicates()
            |> collect(...);

        self::assertSame([
            2 => $a,
        ], $result);
    }

    public function test_works_lazily_with_generators(): void
    {

        $calls = 0;

        $stage = (function () use (&$calls): Generator {
            foreach ([1, 2, 1, 3] as $value) {
                $calls++;
                yield $value;
            }
        })();

        $result = $stage
            |> iterable_duplicates()
            |> iterable_first(...);

        self::assertSame(1, $result);
        self::assertSame(3, $calls);
    }

    public function test_empty_iterable_yields_empty(): void
    {

        $result = []
            |> iterable_duplicates()
            |> collect(...);

        self::assertSame([], $result);
    }
}
