<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_dissoc;

final class ArrayDissocTest extends TestCase
{
    public function test_returns_a_closure() : void
    {
        $stage = array_dissoc('a');

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_removes_a_string_key() : void
    {
        $stage = ['a' => 1, 'b' => 2];

        $result = $stage
            |> array_dissoc('a');

        self::assertSame(['b' => 2], $result);
    }

    public function test_removes_an_int_key() : void
    {
        $stage = [10 => 'x', 20 => 'y', 30 => 'z'];

        $result = $stage
            |> array_dissoc(20);

        self::assertSame([10 => 'x', 30 => 'z'], $result);
    }

    public function test_removes_multiple_keys() : void
    {
        $stage = ['a' => 1, 'b' => 2, 'c' => 3];

        $result = $stage
            |> array_dissoc('a', 'c');

        self::assertSame(['b' => 2], $result);
    }

    public function test_ignores_missing_keys() : void
    {
        $stage = ['a' => 1, 'b' => 2];

        $result = $stage
            |> array_dissoc('nope');

        self::assertSame(['a' => 1, 'b' => 2], $result);
    }

    public function test_does_not_mutate_the_original_array() : void
    {
        $stage = ['a' => 1, 'b' => 2];

        $result = $stage
            |> array_dissoc('a');

        // result has key removed
        self::assertSame(['b' => 2], $result);

        // original remains unchanged
        self::assertSame(['a' => 1, 'b' => 2], $stage);
    }
}
