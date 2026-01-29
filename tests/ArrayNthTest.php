<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_nth;

final class ArrayNthTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = array_nth(0);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_returns_the_nth_element_for_lists(): void
    {

        $stage = [10, 20, 30];

        $result = $stage
            |> array_nth(1);

        self::assertSame(20, $result);
    }

    public function test_returns_the_nth_element_for_associative_arrays_by_iteration_order(): void
    {

        $stage = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
        ];

        $result = $stage
            |> array_nth(2);

        self::assertSame(30, $result);
    }

    public function test_returns_null_when_index_is_out_of_bounds(): void
    {

        $stage = [10, 20, 30];

        $result = $stage
            |> array_nth(99);

        self::assertNull($result);
    }

    public function test_returns_null_for_empty_array(): void
    {

        $result = []
            |> array_nth(0);

        self::assertNull($result);
    }

    public function test_supports_negative_indexes(): void
    {

        $stage = [10, 20, 30];

        $result = $stage
            |> array_nth(-1);

        self::assertSame(30, $result);
    }

    public function test_does_not_mutate_the_input_array(): void
    {

        $stage = [10, 20, 30];
        $before = $stage;

        $result = $stage
            |> array_nth(0);

        self::assertSame(10, $result);
        self::assertSame($before, $stage);
    }

    public function test_throws_type_error_when_index_is_not_an_int(): void
    {

        $this->expectException(\TypeError::class);

        /** @phpstan-ignore-next-line */
        array_nth('nope');
    }
}
