<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\array_all;

final class ArrayAllTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = array_all(static fn (mixed $_, int|string $__) : bool => true);

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_returns_true_for_empty_array_and_does_not_call_predicate() : void {

        $stage = [];

        $calls = 0;

        /** @var bool $result */
        $result = $stage
            |> array_all(static function (mixed $_, int|string $__) use (&$calls) : bool {
                $calls++;
                return false;
            });

        self::assertTrue($result);
        self::assertSame(0, $calls);
    }

    public function test_returns_true_when_predicate_matches_all() : void {

        $stage = [1, 2, 3];

        /** @var bool $result */
        $result = $stage
            |> array_all(static fn (int $v, int|string $k) : bool => \in_array($v, \range(0, 10), true));

        self::assertTrue($result);
    }

    public function test_returns_false_when_predicate_never_matches() : void {

        $stage = [1, 2, 3];

        /** @var bool $result */
        $result = $stage
            |> array_all(static fn (int $v, int|string $k) : bool => $v === 99);

        self::assertFalse($result);
    }

    public function test_returns_false_when_predicate_matches_only_some() : void {

        $stage = [1, 2, 3];

        /** @var bool $result */
        $result = $stage
            |> array_all(static fn (int $v, int|string $k) : bool => $v === 2);

        self::assertFalse($result);
    }

    public function test_passes_array_key_to_predicate() : void {

        $stage = ['a' => 10, 'b' => 20, 'c' => 30];

        $seen = [];

        /** @var bool $result */
        $result = $stage
            |> array_all(static function (int $v, int|string $k) use (&$seen) : bool {
                $seen[] = $k;
                return true;
            });

        self::assertTrue($result);
        self::assertSame(['a', 'b', 'c'], $seen);
    }
}
