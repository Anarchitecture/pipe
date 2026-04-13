<?php

/**
 * PHPStan type-level assertions for utility methods that should preserve types.
 *
 * This file is NOT executed - it is only statically analysed by PHPStan.
 * Each call to assertType() verifies that the inferred type on the right
 * matches the expected string on the left.
 */

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests\PhpstanTypeAssertions;

use Anarchitecture\pipe as p;

use function PHPStan\Testing\assertType;

// ── str_replace: generic TSubject preserved ─────────────────────────

/** @var string $s */
$s = 'hello';
/** @var array<string> $a */
$a = ['hello'];

assertType('string', $s |> p\str_replace('a', 'b'));
assertType('array<string>', $a |> p\str_replace('a', 'b'));

// ── preg_replace: generic TSubject|null preserved ───────────────────

assertType('string|null', $s |> p\preg_replace('/x/', 'y'));
assertType('array<string>|null', $a |> p\preg_replace('/x/', 'y'));

// ── array_map: generic TValue → TResult ─────────────────────────────

/** @var array<string, int> $intMap */
$intMap = ['a' => 1, 'b' => 2];

assertType('array<string, int>', $intMap |> p\array_map(fn(int $n): int => $n * 2));

// ── iterable_map: generic TValue → TResult ──────────────────────────

/** @var iterable<int, string> $strings */
$strings = [10 => 'a', 20 => 'bb'];

assertType('Generator<int, int<0, max>, mixed, mixed>', $strings |> p\iterable_map(fn(string $v): int => strlen($v)));

// ── iterable_filter: preserves generic TValue ───────────────────────

assertType('Generator<int, string, mixed, mixed>', $strings |> p\iterable_filter(fn(string $v, int $k): bool => $k === 10 || $v !== ''));

// ── collect ─────────────────────────────────────────────────────────

/** @var \Generator<int, string> $gen */
assertType('array<int, string>', p\collect($gen));

// ── array_nth / iterable_nth: expose nullability ────────────────────

assertType('int|null', $intMap |> p\array_nth(0));
assertType('string|null', $strings |> p\iterable_nth(1));
