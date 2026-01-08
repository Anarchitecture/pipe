<?php
declare(strict_types=1);

namespace Tests\Unit;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_window;

final class IterableWindowTest extends TestCase
{
    public function testItThrowsWhenSizeIsNegative() : void
    {
        $this->expectException(InvalidArgumentException::class);
        iterable_window(-1);
    }

    public function testItThrowsWhenSizeIsZero() : void
    {
        $this->expectException(InvalidArgumentException::class);
        iterable_window(0);
    }

    public function testItYieldsFullWindowsOnlyFromArray() : void
    {
        $stage = [1, 2, 3, 4, 5, 6];

        $result = $stage
            |> iterable_window(3)
            |> \iterator_to_array(...);

        self::assertSame(
            [
                [1, 2, 3],
                [2, 3, 4],
                [3, 4, 5],
                [4, 5, 6]
            ],
            $result
        );
    }

    public function testItIgnoresInputKeys(): void
    {
        $stage = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
            "d" => 40
        ];

        $result = $stage
            |> iterable_window(2)
            |> \iterator_to_array(...);

        self::assertSame(
            [
                0 => [10, 20],
                1 => [20, 30],
                2 => [30, 40]
            ],
            $result
        );
    }

    public function testItWorksWithGenerators() : void {

        $stage = function () : Generator {
            yield 1;
            yield 2;
            yield 3;
        };

        $result = $stage()
            |> iterable_window(2)
            |> \iterator_to_array(...);

        self::assertSame(
            [
                0 => [1, 2],
                1 => [2, 3]
            ],
            $result
        );
    }

    public function testItYieldsNothingWhenInputShorterThanWindow() : void {

        $stage = [1, 2, 3];

        $result = $stage
            |> iterable_window(4)
            |> \iterator_to_array(...);

        self::assertSame([], $result);
    }
}
