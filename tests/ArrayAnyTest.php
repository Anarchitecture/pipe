<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_any;

final class ArrayAnyTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = array_any(static fn (mixed $_, int|string $__) : bool => true);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_returns_false_for_empty_array_and_does_not_call_predicate() : void {

        $stage = [];

        $calls = 0;

        $result = $stage
            |> array_any(static function (mixed $_, int|string $__) use (&$calls) : bool {
                $calls++;
                return true;
            });

        self::assertFalse($result);
        self::assertSame(0, $calls);
    }

    public function test_returns_true_when_predicate_matches() : void {

        $stage = [1, 2, 3];

        $result = $stage
            |> array_any(static fn (int $v, int|string $k) : bool => $v === 2);

        self::assertTrue($result);
    }

    public function test_returns_false_when_predicate_never_matches() : void {

        $stage = [1, 2, 3];

        $result = $stage
            |> array_any(static fn (int $v, int|string $k) : bool => $v === 99);

        self::assertFalse($result);
    }

    public function test_passes_array_key_to_predicate() : void {

        $stage = ['a' => 10, 'b' => 20, 'c' => 30];

        $seen = [];

        $result = $stage
                |> array_any(static function (int $v, int|string $k) use (&$seen) : bool {
                    $seen[] = $k;
                    return false;
                });

        self::assertFalse($result);
        self::assertSame(['a', 'b', 'c'], $seen);
    }
}
