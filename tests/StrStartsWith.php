<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\str_starts_with;

final class StrStartsWithTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = str_starts_with('foo');

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_returns_true_when_string_starts_with_needle() : void {

        $stage = 'foobar';

        /** @var bool $result */
        $result = $stage
            |> str_starts_with('foo');

        self::assertTrue($result);
    }

    public function test_returns_false_when_string_does_not_start_with_needle() : void {

        $stage = 'foobar';

        /** @var bool $result */
        $result = $stage
            |> str_starts_with('bar');

        self::assertFalse($result);
    }

    public function test_is_case_sensitive() : void {

        $stage = 'Foobar';

        /** @var bool $result */
        $result = $stage
            |> str_starts_with('foo');

        self::assertFalse($result);
    }

    public function test_empty_needle_always_returns_true() : void {

        $stage = 'anything';

        /** @var bool $result */
        $result = $stage
            |> str_starts_with('');

        self::assertTrue($result);
    }
}
