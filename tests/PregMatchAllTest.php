<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\preg_match_all;

final class PregMatchAllTest extends TestCase
{
    public function testItReturnsMatchesInPatternOrderByDefault(): void
    {

        $stage = 'a-1 b-22';

        $result = $stage
            |> preg_match_all('/(\w+)-(\d+)/');

        self::assertSame([
            0 => ['a-1', 'b-22'],
            1 => ['a', 'b'],
            2 => ['1', '22'],
        ], $result);
    }

    public function testItSupportsSetOrder(): void
    {

        $stage = 'a-1 b-22';

        $result = $stage
            |> preg_match_all('/(\w+)-(\d+)/', flags: PREG_SET_ORDER);

        self::assertSame([
            [0 => 'a-1', 1 => 'a', 2 => '1'],
            [0 => 'b-22', 1 => 'b', 2 => '22'],
        ], $result);
    }

    public function testItSupportsOffsetArgument(): void
    {

        $stage = 'x-3 a-1 b-2';

        $result = $stage
            |> preg_match_all('/(\w+)-(\d+)/', offset: 3);

        self::assertSame([
            0 => ['a-1', 'b-2'],
            1 => ['a', 'b'],
            2 => ['1', '2'],
        ], $result);
    }

    public function testItSupportsOffsetCaptureFlag(): void
    {

        $stage = 'a-1 b-22';

        $result = $stage
            |> preg_match_all('/\d+/', flags: PREG_OFFSET_CAPTURE);

        self::assertSame([
            0 => [
                0 => ['1', 2],
                1 => ['22', 6],
            ],
        ], $result);
    }

    public function testItReturnsEmptyMatchArraysWhenNothingMatches(): void
    {

        $stage = 'abc';

        $result = $stage
            |> preg_match_all('/(\d+)/');

        self::assertSame([
            0 => [],
            1 => [],
        ], $result);
    }
}
