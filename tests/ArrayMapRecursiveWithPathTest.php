<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use ArgumentCountError;
use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_map_recursive_with_path;

final class ArrayMapRecursiveWithPathTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = array_map_recursive_with_path(static fn ($v, $path) => $v);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_maps_only_leaves_and_passes_full_path_to_each_leaf() : void {

        $stage = [
            'a' => 1,
            'b' => [
                'c' => 2,
                10  => [3],
            ],
        ];

        $seen = [];

        $callback = static function ($v, array $path) use (&$seen) {
            $seen[] = $path;
            return \is_int($v) ? $v * 10 : $v;
        };

        $result = $stage
            |> array_map_recursive_with_path($callback);

        self::assertSame([
            'a' => 10,
            'b' => [
                'c' => 20,
                10  => [30],
            ],
        ], $result);

        self::assertSame([
            ['a'],
            ['b', 'c'],
            ['b', 10, 0],
        ], $seen);
    }

    public function test_empty_array_returns_empty_and_does_not_call_mapper() : void {

        $calls = 0;

        $callback = static function ($v, $path) use (&$calls) {
            $calls++;
            return $v;
        };

        $result = []
            |> array_map_recursive_with_path($callback);

        self::assertSame([], $result);
        self::assertSame(0, $calls);
    }

    public function test_does_not_mutate_the_input_array() : void {

        $stage = ['a' => [1, 2], 'b' => 3];
        $before = $stage;

        $result = $stage
            |> array_map_recursive_with_path(static fn ($v, $path) => \is_int($v) ? $v + 1 : $v);

        self::assertSame(['a' => [2, 3], 'b' => 4], $result);
        self::assertSame($before, $stage);
    }
}
