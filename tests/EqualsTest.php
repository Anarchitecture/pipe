<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;
use stdClass;

use function Anarchitecture\pipe\equals;
use function Anarchitecture\pipe\iterable_any;

final class EqualsTest extends TestCase
{
    public function test_returns_a_closure() : void {

        $stage = equals(123);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_matches_identical_scalar() : void {
        $stage = equals("hello");

        self::assertTrue($stage("hello"));
        self::assertFalse($stage("Hello"));
    }

    public function test_is_strict_not_loose() : void {

        self::assertFalse(equals(1)("1"));
        self::assertFalse(equals(true)(1));
        self::assertFalse(equals(false)(0));
        self::assertFalse(equals(null)(0));
    }

    public function test_matches_arrays_strictly() : void {

        $pred = equals([1, "2", 3]);

        self::assertTrue($pred([1, "2", 3]));
        self::assertFalse($pred([1, 2, 3]));
        self::assertFalse($pred([1, "2"]));
    }

    public function test_matches_objects_by_identity() : void {

        $o1 = new stdClass();
        $o2 = new stdClass();

        $pred = equals($o1);

        self::assertTrue($pred($o1));
        self::assertFalse($pred($o2));
    }

    public function test_composes_with_iterable_any() : void {
        $stage = [1, 2, 3];


        $result = $stage
            |> iterable_any(equals(2))
            |> boolval(...);

        self::assertTrue($result);
    }
}
