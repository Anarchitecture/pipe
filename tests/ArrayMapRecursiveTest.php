<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_map_recursive;

final class ArrayMapRecursiveTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = array_map_recursive(static fn ($v) => $v);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_maps_only_leaves_and_preserves_keys_at_all_levels() : void {

        $stage = [
            'a' => 1,
            'b' => [
                'c' => 2,
                'd' => [3],
            ],
            10 => 'x',
        ];

        $mapper = static fn(int|string $v) => \is_int($v) ? $v * 10 : $v |> strval(...) |> strtoupper(...);

        $result = $stage
            |> array_map_recursive($mapper);

        self::assertSame([
            'a' => 10,
            'b' => [
                'c' => 20,
                'd' => [30],
            ],
            10 => 'X',
        ], $result);
    }

    public function test_empty_array_returns_empty_and_does_not_call_mapper() : void {

        $calls = 0;

        $mapper = static function ($v) use (&$calls) {
            $calls++;
            return $v;
        };

        $result = []
            |> array_map_recursive($mapper);

        self::assertSame([], $result);
        self::assertSame(0, $calls);
    }

    public function test_does_not_mutate_the_input_array() : void {

        $stage = ['a' => [1, 2], 'b' => 3];
        $before = $stage;

        $result = $stage
            |> array_map_recursive(static fn ($v) => \is_int($v) ? $v + 1 : $v);

        self::assertSame(['a' => [2, 3], 'b' => 4], $result);
        self::assertSame($before, $stage);
    }

}
