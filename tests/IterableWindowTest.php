<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_window;

final class IterableWindowTest extends TestCase
{
    public function testItThrowsWhenSizeIsNegative(): void
    {

        $this->expectException(InvalidArgumentException::class);

        iterable_window(-1);
    }

    public function testItThrowsWhenSizeIsZero(): void
    {

        $this->expectException(InvalidArgumentException::class);

        iterable_window(0);
    }

    public function testItYieldsFullWindowsOnlyFromArray(): void
    {

        $stage = [1, 2, 3, 4, 5, 6];

        $result = $stage
            |> iterable_window(size: 3)
            |> \iterator_to_array(...);

        self::assertSame([
            [1, 2, 3],
            [2, 3, 4],
            [3, 4, 5],
            [4, 5, 6],
        ], $result);
    }

    public function testItIgnoresInputKeys(): void
    {

        $stage = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
            "d" => 40,
        ];

        $result = $stage
            |> iterable_window(size: 2)
            |> \iterator_to_array(...);

        self::assertSame([
            0 => [10, 20],
            1 => [20, 30],
            2 => [30, 40],
        ], $result);
    }

    public function testItWorksWithGenerators(): void
    {

        $stage = function (): Generator {
            yield 1;
            yield 2;
            yield 3;
        };

        $result = $stage()
            |> iterable_window(size: 2)
            |> \iterator_to_array(...);

        self::assertSame(
            [
                0 => [1, 2],
                1 => [2, 3],
            ],
            $result
        );
    }

    public function testItYieldsNothingWhenInputShorterThanWindow(): void
    {

        $stage = [1, 2, 3];

        $result = $stage
            |> iterable_window(size: 4)
            |> \iterator_to_array(...);

        self::assertSame([], $result);
    }

    public function testItYieldsCircularWindowsFromArray(): void
    {

        $stage = [0, 1, 2, -2, -1];

        $result = $stage
            |> iterable_window(size: 4, circular: true)
            |> \iterator_to_array(...);

        self::assertSame([
            [0, 1, 2, -2],
            [1, 2, -2, -1],
            [2, -2, -1, 0],
            [-2, -1, 0, 1],
            [-1, 0, 1, 2],
        ], $result);
    }

    public function testItYieldsCircularWindowsWhenInputLengthEqualsWindowSize(): void
    {

        $stage = [1, 2, 3];

        $result = $stage
            |> iterable_window(size: 3, circular: true)
            |> \iterator_to_array(...);

        self::assertSame([
            [1, 2, 3],
            [2, 3, 1],
            [3, 1, 2],
        ], $result);
    }

    public function testItYieldsNothingWhenCircularInputShorterThanWindow(): void
    {

        $stage = [1, 2, 3];

        $result = $stage
            |> iterable_window(size: 4, circular: true)
            |> \iterator_to_array(...);

        self::assertSame([], $result);
    }

    public function testCircularWindowSizeOneBehavesAsExpected(): void
    {

        $stage = [10, 20, 30];

        $result = $stage
            |> iterable_window(size: 1, circular: true)
            |> \iterator_to_array(...);

        self::assertSame([
            [10],
            [20],
            [30],
        ], $result);
    }

    public function testCircularIgnoresInputKeys(): void
    {
        $stage = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
            'd' => 40,
        ];

        $result = $stage
            |> iterable_window(size: 3, circular: true)
            |> \iterator_to_array(...);

        self::assertSame([
            [10, 20, 30],
            [20, 30, 40],
            [30, 40, 10],
            [40, 10, 20],
        ], $result);
    }

    public function testCircularDoesNotOverwriteEarlierWindowsInIteratorToArray(): void
    {

        $stage = [0, 1, 2, -2, -1];

        $result = $stage
            |> iterable_window(size: 4, circular: true)
            |> \iterator_to_array(...);

        self::assertSame(\count($stage), \count($result));
        self::assertSame(\array_keys($stage), \array_keys($result));
    }
}
