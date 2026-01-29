<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\preg_match;

final class PregMatchTest extends TestCase
{
    public function test_returns_a_closure(): void
    {

        $stage = preg_match('/.*/');

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_returns_matches_for_basic_match(): void
    {

        $stage = 'hello123world';

        $result = $stage
            |> preg_match('/\d+/');

        self::assertSame([
            0 => '123',
        ], $result);
    }

    public function test_returns_empty_array_when_no_match(): void
    {

        $stage = 'hello world';

        $result = $stage
            |> preg_match('/\d+/');

        self::assertSame([], $result);
    }

    public function test_includes_capturing_groups(): void
    {

        $stage = '123abc';

        $result = $stage
            |> preg_match('/(\d+)([a-z]+)/');

        self::assertSame([
            0 => '123abc',
            1 => '123',
            2 => 'abc',
        ], $result);
    }

    public function test_preserves_named_captures(): void
    {

        $stage = 'hello';

        $result = $stage
            |> preg_match('/(?P<word>\w+)/');

        self::assertSame([
            0 => 'hello',
            'word' => 'hello',
            1 => 'hello',
        ], $result);
    }

    public function test_respects_offset(): void
    {

        $stage = 'abc123';

        $result = $stage
            |> preg_match('/\d+/', offset: 4);

        self::assertSame([
            0 => '23',
        ], $result);
    }

    public function test_supports_offset_capture_and_unmatched_as_null(): void
    {

        $stage = 'ab';

        $result = $stage
            |> preg_match('/(ab)(zz)?/', flags: PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL);

        self::assertSame([
            0 => ['ab', 0],
            1 => ['ab', 0],
            2 => [null, -1],
        ], $result);
    }
}
