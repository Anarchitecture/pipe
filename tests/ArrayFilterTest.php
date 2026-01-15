<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_filter;

final class ArrayFilterTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = array_filter(static fn (mixed $_) : bool => true);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_filters_values_and_preserves_keys() : void {

        $stage = [
            10  => 1,
            20  => 0,
            'x' => 2,
        ];

        $result = $stage
            |> array_filter(static fn (int $v) : bool => $v > 0);

        self::assertSame([
            10  => 1,
            'x' => 2,
        ], $result);
    }

    public function test_empty_array_returns_empty_and_does_not_call_predicate() : void {

        $calls = 0;

        $closure = static function (mixed $_) use (&$calls): bool {
            $calls++;
            return true;
        };

        $result = []
            |> array_filter($closure);

        self::assertSame([], $result);
        self::assertSame(0, $calls);
    }

    public function test_does_not_mutate_the_input_array() : void {

        $stage = [1, 0, 2];
        $before = $stage;

        $result = $stage
            |> array_filter(static fn (int $v) : bool => $v > 0);

        self::assertSame([0 => 1, 2 => 2], $result);
        self::assertSame($before, $stage);
    }
}
