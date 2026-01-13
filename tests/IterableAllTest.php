<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_all;

final class IterableAllTest extends TestCase {

    public function test_returns_true_for_empty_iterable() : void {

        $stage = [];

        /** @var bool $result */
        $result = $stage
            |> iterable_all(static fn ($v) : bool => true);

        self::assertTrue($result);
    }

    public function test_returns_true_when_predicate_matches_all() : void {

        $stage = [1, 2, 3];

        /** @var bool $result */
        $result = $stage
            |> iterable_all(static fn (int $v) : bool => in_array($v, range(0,10)));

        self::assertTrue($result);
    }

    public function test_returns_false_when_predicate_never_matches(): void {

        $stage = [1, 2, 3];

        $result = $stage
            |> iterable_all(static fn (int $v) : bool => $v === 99);

        self::assertFalse($result);
    }

    public function test_returns_false_when_predicate_matches_only_some(): void {

        $stage = [1, 2, 3];

        $result = $stage
            |> iterable_all(static fn (int $v) : bool => $v === 2);

        self::assertFalse($result);
    }

    public function test_short_circuits_on_first_non_match(): void {

        $stage = (function () : \Generator {
            for ($i = 1; $i <= 5; $i++) {
                yield $i;
            }
        })();

        /** @var bool $result */
        $result = $stage
            |> iterable_all(static fn(mixed $v): bool => in_array($v, range(1,2)));

        self::assertFalse($result);

        $stage->next();
        self::assertSame(4, $stage->current());
    }

    public function test_null_predicate_checks_strict() : void {

        $stage = [true, true, 1];

        /** @var bool $result */
        $result = $stage
            |> iterable_all();

        self::assertFalse($result);


        $stage = [true, true, "string"];

        /** @var bool $result */
        $result = $stage
            |> iterable_all();

        self::assertFalse($result);

        $stage = [true, true, true];

        /** @var bool $result */
        $result = $stage
            |> iterable_all();

        self::assertTrue($result);

    }
}
