<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use Closure;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\collect;
use function Anarchitecture\pipe\explode;

final class ExplodeTest extends TestCase {

    public function test_returns_a_closure() : void {

        $stage = explode(',');

        self::assertInstanceOf(Closure::class, $stage);
    }

    public function test_splits_a_string() : void {

        $stage = 'a,b,c';

        $result = $stage
            |> explode(',')
            |> collect(...);

        self::assertSame(['a', 'b', 'c'], $result);
    }

    public function test_preserves_empty_segments() : void {

        $stage = 'a,,c,';

        $result = $stage
            |> explode(',')
            |> collect(...);

        self::assertSame(['a', '', 'c', ''], $result);
    }

    public function test_positive_limit_behaves_like_php_explode() : void {

        $stage = 'a,b,c,d';

        $result = $stage
            |> explode(',', 2)
            |> collect(...);

        self::assertSame(['a', 'b,c,d'], $result);
    }

    public function test_negative_limit_drops_last_n_parts() : void {

        $stage = 'a,b,c,d';

        $result = $stage
            |> explode(',', -1)
            |> collect(...);

        self::assertSame(['a', 'b', 'c'], $result);
    }
}
