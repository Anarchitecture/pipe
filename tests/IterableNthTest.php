<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_nth;

final class IterableNthTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = iterable_nth(0);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_returns_nth_element_from_array() : void {

        $result = [10, 20, 30]
            |> iterable_nth(1);

        self::assertSame(20, $result);
    }

    public function test_short_circuits_and_does_not_iterate_past_nth() : void {

        $calls = 0;

        $generator = (static function () use (&$calls) : \Generator {
            foreach ([10, 20, 30, 40] as $v) {
                $calls++;
                yield $v;
            }
        })();

        $result = $generator
            |> iterable_nth(2);

        self::assertSame(30, $result);
        self::assertSame(3, $calls); // consumed exactly n+1 elements
    }

    public function test_throws_when_n_is_negative() : void {

        $this->expectException(InvalidArgumentException::class);
        iterable_nth(-1);
    }

    public function test_null_when_iterable_is_too_short() : void {

        $result = [1, 2]
            |> iterable_nth(5);

        self::assertNull($result);
    }
}
