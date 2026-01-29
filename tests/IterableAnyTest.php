<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\iterable_any;

final class IterableAnyTest extends TestCase
{
    public function test_returns_false_for_empty_iterable(): void
    {

        $stage = [];

        $result = $stage
            |> iterable_any(static fn($v): bool => true);

        self::assertFalse($result);
    }

    public function test_returns_true_when_predicate_matches(): void
    {

        $stage = [1, 2, 3];

        /** @var bool $result */
        $result = $stage
            |> iterable_any(static fn(int $v): bool => $v === 2);

        self::assertTrue($result);
    }

    public function test_returns_false_when_predicate_never_matches(): void
    {

        $stage = [1, 2, 3];

        $result = $stage
            |> iterable_any(static fn(int $v): bool => $v === 99);

        self::assertFalse($result);
    }

    public function test_short_circuits_on_first_match(): void
    {

        $stage = (function (): \Generator {
            for ($i = 1; $i <= 5; $i++) {
                yield $i;
            }
        })();

        /** @var bool $result */
        $result = $stage
            |> iterable_any(static fn(mixed $v): bool => $v === 3);

        self::assertTrue($result);

        $stage->next();
        self::assertSame(4, $stage->current());
    }

    public function test_null_predicate_checks_strict(): void
    {

        $stage = [0, 0, 1];

        /** @var bool $result */
        $result = $stage
            |> iterable_any();

        self::assertFalse($result);

        $stage = [0, true, false];

        /** @var bool $result */
        $result = $stage
            |> iterable_any();

        self::assertTrue($result);

        $stage = [1, '', false];

        /** @var bool $result */
        $result = $stage
            |> iterable_any();

        self::assertFalse($result);
    }
}
