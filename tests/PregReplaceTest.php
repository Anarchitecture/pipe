<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\TestStatus\Warning;
use function Anarchitecture\pipe\preg_replace;

final class PregReplaceTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = preg_replace('/x/', 'y');

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_replaces_in_a_string_subject() : void {

        $stage = 'axbxc';

        $result = $stage
            |> preg_replace('/x/', 'Z');

        self::assertSame('aZbZc', $result);
    }

    public function test_respects_limit() : void {

        $stage = 'axbxcxd';

        $result = $stage
            |> preg_replace('/x/', 'Z', 2);

        self::assertSame('aZbZcxd', $result);
    }

    public function test_replaces_in_an_array_subject() : void {

        $stage = ['a1', 'b2', 'c3'];

        $result = $stage
            |> preg_replace('/\d/', 'X');

        self::assertSame(['aX', 'bX', 'cX'], $result);
    }

    public function test_supports_array_patterns_and_replacements() : void {

        $stage = 'foo 123 bar';

        $result = $stage
            |> preg_replace(
                ['/\bfoo\b/', '/\d+/'],
                ['qux', 'N']
            );

        self::assertSame('qux N bar', $result);
    }

}
