<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_string;

final class IterableStringTest extends TestCase
{
    public function test_it_throws_for_size_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        iterable_string(0);
    }

    public function test_it_throws_for_size_negative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        iterable_string(-1);
    }

    public function test_size_1_yields_bytes_with_byte_offset_keys(): void
    {
        $stage = "abcdef";

        $result = $stage
            |> iterable_string(1)
            |> \iterator_to_array(...);

        self::assertSame([
                0 => 'a',
                1 => 'b',
                2 => 'c',
                3 => 'd',
                4 => 'e',
                5 => 'f'
        ], $result);
    }

    public function test_size_1_handles_empty_string(): void
    {
        $stage = "";

        $result = $stage
            |> iterable_string(1)
            |> \iterator_to_array(...);

        self::assertSame([], $result);
    }

    public function test_size_n_yields_chunks_with_byte_offset_keys_even_length(): void
    {
        $stage = "abcdef";

        $result = $stage
            |> iterable_string(2)
            |> \iterator_to_array(...);

        self::assertSame([
            0 => 'ab',
            2 => 'cd',
            4 => 'ef',
        ], $result);
    }

    public function test_size_n_yields_last_short_chunk_for_odd_length(): void
    {
        $stage = "abcde";

        $result = $stage
            |> iterable_string(2)
            |> \iterator_to_array(...);

        self::assertSame([
            0 => 'ab',
            2 => 'cd',
            4 => 'e',
        ], $result);
    }

    public function test_size_larger_than_length_yields_single_chunk(): void
    {
        $stage = "abcde";

        $result = $stage
            |> iterable_string(10)
            |> \iterator_to_array(...);

        self::assertSame([
            0 => 'abcde',
        ], $result);
    }

    public function test_size_n_handles_empty_string(): void
    {
        $stage = "";

        $result = $stage
            |> iterable_string(10)
            |> \iterator_to_array(...);

        self::assertSame([], $result);
    }

    public function test_it_is_binary_safe_with_nul_bytes_size_1(): void
    {
        $stage = "a\0b";

        $result = $stage
            |> iterable_string(1)
            |> \iterator_to_array(...);

        self::assertSame(3, \strlen($stage));
        self::assertSame([
            0 => "a",
            1 => "\0",
            2 => "b",
        ], $result);
    }

    public function test_it_is_binary_safe_with_nul_bytes_chunked(): void
    {
        $stage = "a\0b";

        $result = $stage
            |> iterable_string(2)
            |> \iterator_to_array(...);

        self::assertSame([
            0 => "a\0",
            2 => "b",
        ], $result);
    }
}
