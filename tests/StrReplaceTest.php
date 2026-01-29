<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\str_replace;

final class StrReplaceTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = str_replace('a', 'b');

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_replaces_in_a_string_subject(): void
    {

        $stage = 'abracadabra';

        $result = $stage
            |> str_replace('a', 'A');

        self::assertSame('AbrAcAdAbrA', $result);
    }

    public function test_can_replace_multiple_search_values(): void
    {

        $stage = 'foo bar baz';

        $result = $stage
            |> str_replace(['foo', 'baz'], ['qux', 'quux']);

        self::assertSame('qux bar quux', $result);
    }

    public function test_replaces_in_an_array_subject(): void
    {

        $stage = ['a1', 'b2', 'c3'];

        $result = $stage
            |> str_replace('2', 'X');

        self::assertSame(['a1', 'bX', 'c3'], $result);
    }

    public function test_replacing_with_empty_string_removes_matches(): void
    {

        $stage = 'a-b-c';

        $result = $stage
            |> str_replace('-', '');

        self::assertSame('abc', $result);
    }
}
