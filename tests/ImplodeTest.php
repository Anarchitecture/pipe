<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\implode;

final class ImplodeTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = implode(',');

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_joins_an_array_with_separator(): void
    {

        $stage = ['a', 'b', 'c'];

        $result = $stage
            |> implode(',');

        self::assertSame('a,b,c', $result);
    }

    public function test_empty_separator_concatenates(): void
    {

        $stage = ['a', 'b', 'c'];

        $result = $stage
            |> implode('');

        self::assertSame('abc', $result);
    }

    public function test_preserves_empty_strings(): void
    {

        $stage = ['a', '', 'c', ''];

        $result = $stage
            |> implode(',');

        self::assertSame('a,,c,', $result);
    }

    public function test_uses_values_in_iteration_order_for_associative_arrays(): void
    {

        $stage = [
            'x' => 'a',
            'y' => 'b',
            'z' => 'c',
        ];

        $result = $stage
            |> implode('-');

        self::assertSame('a-b-c', $result);
    }
}
