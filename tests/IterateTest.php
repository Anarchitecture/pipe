<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Generator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_take;
use function Anarchitecture\pipe\iterate;

final class IterateTest extends TestCase
{
    public function test_iterate_includes_seed_by_default(): void
    {

        $stage = 0;

        $result = $stage
            |> iterate(static fn(int $x): int => $x + 1)
            |> iterable_take(4)
            |> iterator_to_array(...);

        self::assertSame([
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
        ], $result);
    }

    public function test_iterate_can_exclude_seed(): void
    {

        $stage = 0;

        $result = $stage
            |> iterate(static fn(int $x): int => $x + 1, false)
            |> iterable_take(4)
            |> iterator_to_array(...);

        self::assertSame([
            0 => 1,
            1 => 2,
            2 => 3,
            3 => 4,
        ], $result);
    }

    public function test_iterate_is_lazy_and_only_calls_callback_for_values_consumed(): void
    {

        $stage = "a";

        /** @var Generator $iterator */
        $iterator = $stage
            |> iterate(static fn(string $a): string => $a . $a);

        $result = $iterator
            |> iterable_take(3)
            |> iterator_to_array(...);

        self::assertSame([
            0 => 'a',
            1 => 'aa',
            2 => 'aaaa',
        ], $result);

        self::assertSame("aaaa", $iterator->current());
    }
}
