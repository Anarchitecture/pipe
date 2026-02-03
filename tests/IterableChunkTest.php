<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\collect;
use function Anarchitecture\pipe\iterable_chunk;

final class IterableChunkTest extends TestCase
{
    public function testItThrowsWhenSizeIsNegative(): void
    {

        $this->expectException(InvalidArgumentException::class);

        iterable_chunk(-1);
    }

    public function testItThrowsWhenSizeIsZero(): void
    {

        $this->expectException(InvalidArgumentException::class);

        iterable_chunk(0);
    }

    public function testItYieldsChunksFromArray(): void
    {

        $stage = [1, 2, 3, 4, 5];

        $result = $stage
            |> iterable_chunk(size: 2)
            |> collect(...);

        self::assertSame([
            0 => [1, 2],
            1 => [3, 4],
            2 => [5],
        ], $result);
    }

    public function testItIgnoresInputKeysByDefault(): void
    {

        $stage = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
            'd' => 40,
        ];

        $result = $stage
            |> iterable_chunk(size: 2)
            |> collect(...);

        self::assertSame([
            0 => [10, 20],
            1 => [30, 40],
        ], $result);
    }

    public function testItPreservesKeysWhenEnabled(): void
    {

        $stage = [
            10  => 'a',
            'x' => 'b',
            30  => 'c',
            'y' => 'd',
        ];

        $result = $stage
            |> iterable_chunk(size: 2, preserve_keys: true)
            |> collect(...);

        self::assertSame([
            0 => [
                10  => 'a',
                'x' => 'b',
            ],
            1 => [
                30  => 'c',
                'y' => 'd',
            ],
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
            |> iterable_chunk(size: 2)
            |> collect(...);

        self::assertSame([
            0 => [1, 2],
            1 => [3],
        ], $result);
    }

    public function testItYieldsNothingWhenInputIsEmpty(): void
    {

        $stage = [];

        $result = $stage
            |> iterable_chunk(size: 3)
            |> collect(...);

        self::assertSame([], $result);
    }

    public function testPreserveKeysStillChunksByItemsConsumed(): void
    {

        $stage = function (): Generator {
            yield 'a' => 1;
            yield 'a' => 2;
            yield 'b' => 3;
            yield 'c' => 4;
        };

        $result = $stage()
            |> iterable_chunk(size: 2, preserve_keys: true)
            |> collect(...);

        self::assertSame([
            0 => ['a' => 2],
            1 => ['b' => 3, 'c' => 4],
        ], $result);
    }
}
