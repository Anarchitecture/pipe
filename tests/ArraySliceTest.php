<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_slice;

final class ArraySliceTest extends TestCase
{
    public function test_returns_a_closure() : void {

        $stage = array_slice(1);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_slices_with_offset_and_length_and_reindexes_numeric_keys_by_default() : void {

        $stage = [
            10 => 'a',
            20 => 'b',
            30 => 'c',
            40 => 'd',
        ];

        $result = $stage
            |> array_slice(1, 2);

        self::assertSame([
            0 => 'b',
            1 => 'c'
        ], $result);
    }

    public function test_preserves_numeric_keys_when_enabled() : void {

        $stage = [
            10 => 'a',
            20 => 'b',
            30 => 'c',
            40 => 'd',
        ];

        $result = $stage
            |> array_slice(1, 2, true);

        self::assertSame([
            20 => 'b',
            30 => 'c',
        ], $result);
    }

    public function test_null_length_slices_to_the_end() : void {

        $stage = [1, 2, 3, 4];

        $result = $stage
                |> array_slice(2);

        self::assertSame([3, 4], $result);
    }

    public function test_supports_negative_offset_and_length() : void {

        $stage = [1, 2, 3, 4, 5];

        $result = $stage
            |> array_slice(-3, 2);

        self::assertSame([3, 4], $result);
    }

    public function test_does_not_mutate_the_input_array() : void {

        $stage = [1, 2, 3, 4];
        $before = $stage;

        $result = $stage
            |> array_slice(1, 2);

        self::assertSame([2, 3], $result);
        self::assertSame($before, $stage);
    }

    public function test_always_preserves_string_keys_even_when_preserve_keys_is_false() : void {

        $stage = [
            'first' => 'a',
            20      => 'b',
            'third' => 'c',
            40      => 'd',
        ];

        $result = $stage
            |> array_slice(1, 2, false);

        self::assertSame([
            0       => 'b',
            'third' => 'c',
        ], $result);
    }


}
