<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_allocate;

final class IterableAllocateTest extends TestCase {

    public function testItThrowsWhenTotalIsNegative() : void {

        $this->expectException(InvalidArgumentException::class);

        iterable_allocate(-1);
    }

    public function testEmptyInputTotalZeroYieldsOneEmptyAllocation() : void {

        $stage = [];

        $result = $stage
            |> iterable_allocate(0)
            |> iterator_to_array(...);

        self::assertSame([
            0 => []
        ], $result);
    }

    public function testEmptyInputTotalNonZeroYieldsNothing() : void {

        $stage = [];

        $result = $stage
            |> iterable_allocate(3)
            |> iterator_to_array(...);

        self::assertSame([], $result);
    }

    public function testItPreservesStringKeysAndYieldsAllAllocationsInExpectedOrder() : void {

        $stage = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
        ];

        $result = $stage
            |> iterable_allocate(2)
            |> iterator_to_array(...);

        self::assertSame([
            0 => ['a' => 0, 'b' => 0, 'c' => 2],
            1 => ['a' => 0, 'b' => 1, 'c' => 1],
            2 => ['a' => 0, 'b' => 2, 'c' => 0],
            3 => ['a' => 1, 'b' => 0, 'c' => 1],
            4 => ['a' => 1, 'b' => 1, 'c' => 0],
            5 => ['a' => 2, 'b' => 0, 'c' => 0],
        ], $result);
    }

    public function testItWorksWithNumericKeys() : void {

        $stage = [
            10, 20, 30,
        ];

        $result = $stage
            |> iterable_allocate(2)
            |> iterator_to_array(...);

        self::assertSame([
            0 => [0 => 0, 1 => 0, 2 => 2],
            1 => [0 => 0, 1 => 1, 2 => 1],
            2 => [0 => 0, 1 => 2, 2 => 0],
            3 => [0 => 1, 1 => 0, 2 => 1],
            4 => [0 => 1, 1 => 1, 2 => 0],
            5 => [0 => 2, 1 => 0, 2 => 0],
        ], $result);
    }

    public function testItWorksWithGeneratorInputAndPreservesKeys() : void {

        $stage = static function () : Generator {
            yield 'x' => 123;
            yield 'y' => 456;
        };

        $result = $stage()
            |> iterable_allocate(3)
            |> iterator_to_array(...);

        self::assertSame([
            0 => ['x' => 0, 'y' => 3],
            1 => ['x' => 1, 'y' => 2],
            2 => ['x' => 2, 'y' => 1],
            3 => ['x' => 3, 'y' => 0],
        ], $result);
    }

    public function testEveryAllocationSumsToTotal() : void {

        $stage = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ];

        $total = 7;

        foreach (($stage |> iterable_allocate($total)) as $allocation) {
            self::assertSame($total, array_sum($allocation));
        }
    }

    public function testCountMatchesStarsAndBars() : void {

        /// n = 4 items, total = 3
        // Number of non-negative integer solutions to x1+x2+x3+x4 = 3 is:
        // binom(total + n - 1, n - 1) = binom(3 + 4 - 1, 4 - 1) = binom(6, 3)
        // = 6! / (3!*3!) = 20

        $stage = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ];

        $result = $stage
            |> iterable_allocate(3)
            |> iterator_to_array(...);

        self::assertCount(20, $result);
    }
}