<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_chunk;

final class ArrayChunkTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = array_chunk(2);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_chunks_array_into_lists() : void {

        $stage = [1, 2, 3, 4, 5];

        $result = $stage
            |> array_chunk(2);

        self::assertSame([
            [1, 2],
            [3, 4],
            [5],
        ], $result);
    }

    public function test_reindexes_keys_by_default() : void {

        $stage = [
            10 => 'a',
            20 => 'b',
            30 => 'c',
        ];

        $result = $stage
            |> array_chunk(2);

        self::assertSame([
            [0 => 'a', 1 => 'b'],
            [0 => 'c'],
        ], $result);
    }

    public function test_reindexes_string_keys_by_default() : void {

        $stage = [
            'x' => 1,
            'y' => 2,
            'z' => 3,
        ];

        $result = $stage
            |> array_chunk(2);

        self::assertSame([
            [0 => 1, 1 => 2],
            [0 => 3],
        ], $result);
    }

    public function test_preserves_keys_when_enabled() : void {

        $stage = [
            10   => 'a',
            'x'  => 'b',
            30   => 'c',
            'y'  => 'd',
        ];

        $result = $stage
                |> array_chunk(2, true);

        self::assertSame([
            [
                10  => 'a',
                'x' => 'b',
            ],
            [
                30  => 'c',
                'y' => 'd',
            ],
        ], $result);
    }

    public function test_returns_empty_array_for_empty_input() : void {

        $stage = [];

        $result = $stage
            |> array_chunk(3);

        self::assertSame([], $result);
    }

    public function test_throws_value_error_when_length_is_zero() : void {

        $this->expectException(\ValueError::class);

        $stage = [1, 2, 3];

        /** @phpstan-ignore-next-line */
        $stage |> array_chunk(0);
    }

    public function test_does_not_mutate_the_input_array() : void {

        $stage = [1, 2, 3, 4];
        $before = $stage;

        $result = $stage
            |> array_chunk(2);

        self::assertSame([
            [1, 2],
            [3, 4],
        ], $result);

        self::assertSame($before, $stage);
    }
}
