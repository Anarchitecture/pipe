<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use ArrayIterator;
use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\collect;

final class CollectTest extends TestCase
{
    public function test_collects_array_and_preserves_keys(): void
    {

        $stage = [10 => 'a', 20 => 'b', 'x' => 'y'];

        $result = collect($stage);

        self::assertSame($stage, $result);
    }

    public function test_collects_generator_and_preserves_keys(): void
    {

        $stage = (function (): \Generator {
            yield 'a' => 1;
            yield 10 => 2;
            yield 'z' => 3;
        })();

        $result = collect($stage);

        self::assertSame(['a' => 1, 10 => 2, 'z' => 3], $result);
    }

    public function test_collects_iterator(): void
    {

        $stage = new ArrayIterator(['k1' => 'v1', 'k2' => 'v2']);

        $result = collect($stage);

        self::assertSame(['k1' => 'v1', 'k2' => 'v2'], $result);
    }
}
