<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_first;

final class IterableFirstTest extends TestCase {

    public function test_returns_first_value_from_array() : void {

        $stage = [1, 2, 3];

        $result = iterable_first($stage);

        self::assertSame(1, $result);
    }

    public function test_ignores_keys_and_returns_first_value() : void {

        $stage = [
            'a' => 10,
            'b' => 20,
        ];

        $result = iterable_first($stage);

        self::assertSame(10, $result);
    }

    public function test_returns_null_for_empty_iterable() : void {

        $stage = [];

        $result = iterable_first($stage);

        self::assertNull($result);
    }

    public function test_returns_first_value_from_generator_and_consumes_one_element() : void {

        $stage = (function () : \Generator {
            for ($i = 1; $i <= 5; $i++) {
                yield $i;
            }
        })();

        $result = iterable_first($stage);

        self::assertSame(1, $result);

        $stage->next();
        self::assertSame(2, $stage->current());
    }


    public function test_returns_null_for_empty_generator() : void {

        $stage = (function () : \Generator {
            /** @phpstan-ignore-next-line */
            if (false) {
                yield 1;
            }

            return;
        })();

        $result = iterable_first($stage);

        self::assertNull($result);
    }

}
