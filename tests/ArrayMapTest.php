<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_map;

final class ArrayMapTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = array_map(static fn($v) => $v);

        self::assertInstanceOf(Closure::class, $stage) ;
    }

    public function test_maps_values_and_preserves_keys_for_single_array(): void
    {

        $input = [
            10 => 1,
            20 => 2,
            'x' => 3,
        ];

        $result = $input
            |> array_map(static fn(int $v) => $v * 2);

        self::assertSame([
            10 => 2,
            20 => 4,
            'x' => 6,
        ], $result);
    }

    public function test_does_not_mutate_the_input_array(): void
    {

        $input = [1, 2, 3];
        $before = $input;

        $result = $input
            |> array_map(static fn(int $v) => $v + 10);

        self::assertSame([11, 12, 13], $result);
        self::assertSame($before, $input);
    }

    public function test_empty_array_returns_empty_and_does_not_call_mapper(): void
    {

        $calls = 0;

        $result = []
            |> array_map(static function ($v) use (&$calls) {
                $calls++;
                return $v;
            });

        self::assertSame([], $result);
        self::assertSame(0, $calls);
    }
}
