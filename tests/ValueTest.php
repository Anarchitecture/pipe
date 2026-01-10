<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\value;
use function Anarchitecture\pipe\when;
use function Anarchitecture\pipe\array_map;

final class ValueTest extends TestCase {

    public function test_returns_a_closure() : void {
        self::assertInstanceOf(Closure::class, value(123));
    }

    public function test_returns_constant_ignoring_input() : void {

        $stage = value("x");

        self::assertSame("x", $stage(null));
        self::assertSame("x", $stage(123));
        self::assertSame("x", $stage(["anything"]));
        self::assertSame("x", $stage((object) ["a" => 1]));
    }

    public function test_captures_value_at_creation_time() : void {

        $stage = 1;
        $callable = value($stage);
        $stage = 2;

        self::assertSame(1, $callable("ignored"));
    }

    public function test_composes_with_when() : void {

        $stage = "hello";

        $result = $stage
            |> when(is_string(...), value("world"));

        self::assertSame("world", $result);
    }

    public function test_composes_with_array_map() : void {

        $result = [1, 2, 3]
            |> array_map(value(9));

        self::assertSame([9, 9, 9], $result);
    }
}
