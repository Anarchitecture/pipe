<?php

declare(strict_types=1);

namespace Anarchitecture\pipe;

use Closure;
use Generator;
use InvalidArgumentException;
use OutOfBoundsException;

/**
 * Apply a callback to an array of arguments.
 *
 * - Integer-keyed arrays (including sparse) are applied positionally in iteration order.
 * - String-keyed arrays are applied as named arguments.
 * - Mixed integer and string keys are not supported. The returned closure will throw InvalidArgumentException
 *
 * @param callable $callback
 * @return Closure(array<int, mixed>|array<string, mixed>): mixed
 */
function apply(callable $callback) : Closure {
    return static function (array $array) use ($callback) : mixed {

        $has_int = false;
        $has_string = false;

        foreach ($array as $k => $_) {

            if (\is_int($k)) {
                $has_int = true;
            }

            else {
                $has_string = true;
            }

            if ($has_int && $has_string) {
                throw new InvalidArgumentException('mixed numeric and associative keys are not supported');
            }
        }

        return $callback(...$array);
    };
}

/**
 * Return unary callable for array_all
 *
 * @param callable $callback
 * @return Closure(array<array-key, mixed>) : bool
 */
function array_all(callable $callback) : Closure {
    return function (array $array) use ($callback) : bool {
        return \array_all($array, $callback);
    };
}

/**
 * Return unary callable for array_any
 *
 * @param callable $callback
 * @return Closure(array<array-key, mixed>) : bool
 */
function array_any(callable $callback) : Closure {
    return function (array $array) use ($callback) : bool {
        return \array_any($array, $callback);
    };
}

/**
 * Return unary callable for array_chunk
 *
 * @param int<1, max> $length
 * @param bool $preserve_keys
 * @return Closure(array<array-key, mixed>): list<array<array-key, mixed>>
 */
function array_chunk(int $length, bool $preserve_keys = false) : Closure {
    return function (array $array) use ($length, $preserve_keys) : array {
        return \array_chunk($array, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for removing one or more keys from an array.
 *
 * This is the associative-array equivalent of a "remove" operation:
 * it returns a new array value with the given keys removed.
 *
 * @param array-key ...$keys
 * @return Closure(array<array-key, mixed>): array<array-key, mixed>
 */
function array_dissoc(string|int ...$keys) : Closure {
    return static function (array $array) use ($keys) : array {
        foreach ($keys as $key) {
            unset($array[$key]);
        }
        return $array;
    };
}

/**
 * Return unary callable for array_filter - values only
 *
 * @param callable $callback
 * @param int $mode
 * @return Closure(array<array-key, mixed>) : array<array-key, mixed>
 */
function array_filter(callable $callback, int $mode = 0) : Closure {
    return function (array $array) use ($callback, $mode) : array {
        return \array_filter($array, $callback, $mode);
    };
}

/**
 *  Flatten an array of arrays by one level.
 *
 *  Semantics match \array_merge(...$arrays):
 *  - String keys are preserved; if the same string key appears multiple times, the *later* value wins.
 *  - Numeric keys are reindexed.
 *  - The input must be a list/array of arrays (non-arrays will cause a TypeError via the spread).
 *  - Empty input array returns empty output array
 *
 * @param array<array<array-key, mixed>> $array
 * @return array<array-key, mixed>
 */
function array_flatten(array $array) : array {
    return $array === [] ? [] : \array_merge(...$array);
}

/**
 * Return unary callable for array_map
 *
 * @param callable $callback
 * @return Closure(array<array-key, mixed>) : array<array-key, mixed>
 */
function array_map(callable $callback) : Closure {
    return function (array $array) use ($callback) : array {
        return \array_map($callback, $array);
    };
}

/**
 * Return unary callable for recursively mapping an array (leaves only).
 *
 * Traverses arrays only. The mapper is applied to non-array leaf values.
 * Preserves keys at every level.
 *
 * @param callable $callback
 * @return Closure(array<array-key, mixed>): array<array-key, mixed>
 */
function array_map_recursive(callable $callback) : Closure {

    return static function (array $array) use ($callback) : array {

        $array_map_recursive = static function (array $array) use ($callback, &$array_map_recursive) : array {

            return \array_map(fn ($value) => \is_array($value) ? $array_map_recursive($value) : $callback($value), $array);

        };

        return $array_map_recursive($array);
    };
}

/**
 * Return unary callable for recursively mapping an array (leaves only), passing the leaf path.
 *
 * Traverses arrays only. The mapper is applied to non-array leaf values as:
 *   $callback($value, $path)
 * where $path is a list of keys from the root to the leaf (including the leaf key).
 *
 * Preserves keys at every level.
 *
 * @param callable $callback
 * @return Closure(array<array-key, mixed>): array<array-key, mixed>
 */
function array_map_recursive_with_path(callable $callback) : Closure {

    return static function (array $array) use ($callback) : array {

        $array_map_recursive_with_path = static function (array $array, array $path = []) use ($callback, &$array_map_recursive_with_path) : array {

            $out = [];

            foreach ($array as $key => $value) {
                $next_path = [...$path, $key];

                $out[$key] = \is_array($value)
                    ? $array_map_recursive_with_path($value, $next_path)
                    : $callback($value, $next_path);
            }

            return $out;
        };

        return $array_map_recursive_with_path($array, []);
    };
}

/**
 * Return unary callable for returning the nth element of an array.
 *
 * Semantics match `\array_slice($array, $i, 1)`:
 * - $i >= 0 selects from the start (0-based).
 * - $i < 0 selects from the end (-1 = last, -2 = second-last, ...).
 * - Out of bounds returns null.
 *
 * @param int $i
 * @return Closure(array<array-key, mixed>): (mixed|null)
 */
function array_nth(int $i) : Closure {
    return function (array $array) use ($i) : mixed {
        return $array
            |> array_slice($i, 1)
            |> \array_first(...);
    };
}


/**
 * Return unary callable for array_reduce
 *
 * The reducer is called as: $callback($carry, $value) (no array key is provided).
 * For an empty array, the result is $initial (or null if omitted).
 *
 * @param callable $callback
 * @param mixed $initial
 * @return Closure(array<array-key, mixed>): (mixed|null)
 */
function array_reduce(callable $callback, mixed $initial = null) : Closure {
    return function (array $array) use ($callback, $initial) : mixed {
        return \array_reduce($array, $callback, $initial);
    };
}

/**
 * Return unary callable for reducing an array until $until returns true.
 *
 *  On each element (left-to-right):
 *    $carry = $callback($carry, $value, $key);
 *    if ($until($carry, $value, $key)) stop and return [$carry, $key, $value].
 *
 *  Short-circuits: once triggered, no further elements are processed.
 *  If never triggered (including empty input), returns [$carry, null, null]
 *  where $carry is the final carry (or $initial for empty input).
 *
 * @param callable $callback
 * @param callable $until
 * @param mixed $initial
 * @return Closure(array<array-key, mixed>): array{0:mixed, 1:array-key|null, 2:mixed|null}
 */
function array_reduce_until(callable $callback, callable $until, mixed $initial = null) : Closure {
    return function (array $array) use ($callback, $until, $initial) : array {
        $carry = $initial;

        foreach ($array as $key => $value) {
            $carry = $callback($carry, $value, $key);

            if ($until($carry, $value, $key) === true) {
                return [$carry, $key, $value];
            }
        }

        return [$carry, null, null];
    };
}

/**
 * Return unary callable for array_slice
 *
 * @param int $offset
 * @param int|null $length
 * @param bool $preserve_keys
 * @return Closure(array<array-key, mixed>): array<array-key, mixed>
 */
function array_slice(int $offset, ?int $length = null, bool $preserve_keys = false) : Closure {
    return function (array $array) use ($offset, $length, $preserve_keys) : array {
        return \array_slice($array, $offset, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for mapping over an array and summing the results.
 *
 * Equivalent to:
 *   fn(array $xs) => array_reduce($xs, fn($sum, $x) => $sum + $callback($x), 0)
 *
 * @param callable $callback
 * @return Closure
 */
function array_sum(callable $callback) : Closure {
    return function (array $array) use ($callback) {
        $sum = 0;

        foreach ($array as $value) {
            /** @var int|float $result */
            $result = $callback($value);
            $sum += $result;
        }

        return $sum;
    };
}

/**
 * Return unary callable for transposing a 2D array (matrix).
 *
 * This is similar to `\array_map(null, ...$arrays)` for rectangular numeric matrices,
 * but it *preserves keys*:
 * - Column keys from the input rows become row keys in the result.
 * - Row keys from the input become column keys in the result.
 *
 * Missing cells are padded with null.
 * Column key order is the "first-seen" order while scanning rows top-to-bottom.
 *
 * @return Closure(array<array-key, array<array-key, mixed>>): array<array-key, array<array-key, mixed>>
 */
function array_transpose() : Closure {

    return static function (array $arrays) : array {
        /** @var array<array-key, array<array-key, mixed>> $arrays */

        if ($arrays === []) {
            return [];
        }

        $row_keys = array_keys($arrays);

        $column_keys = [];

        /** @var array<array-key, mixed> $row */
        foreach ($arrays as $row) {
            foreach (array_keys($row) as $column_key) {
                $column_keys[$column_key] = true;
            }
        }

        $column_keys = array_keys($column_keys);

        $matrix = [];

        /** @var array-key $column_key */
        foreach ($column_keys as $column_key) {

            $row = [];

            /** @var array-key $row_key */
            foreach ($row_keys as $row_key) {

                $var = $arrays[$row_key][$column_key] ?? null;

                $row[$row_key] = $var;
            }

            $matrix[$column_key] = $row;
        }

        return $matrix;
    };
}

/**
 * Return unary callable for array_unique
 *
 * Removes duplicate values and preserves the key of the first occurrence.
 * Value comparison depends on $flags (default: SORT_STRING).
 *
 * @param int $flags One of SORT_STRING, SORT_REGULAR, SORT_NUMERIC, SORT_LOCALE_STRING
 * @return Closure(array<array-key, mixed>): array<array-key, mixed>
 * @throws \ValueError when $flags is not a supported SORT_* mode for array_unique
 */
function array_unique(int $flags = \SORT_STRING) : Closure {
    $allowed = [
        \SORT_STRING,
        \SORT_REGULAR,
        \SORT_NUMERIC,
        \SORT_LOCALE_STRING,
    ];

    if (!\in_array($flags, $allowed, true)) {
        throw new \ValueError('Invalid $flags for array_unique');
    }

    return function (array $array) use ($flags) : array {
        return \array_unique($array, $flags);
    };
}

/**
 * Collect an iterable into an array while preserving keys.
 *
 * This is a terminal helper intended for use at the end of a pipe. It accepts any
 * iterable (arrays or Traversable) and returns a plain PHP array with the same
 * keys and values.
 *
 * Useful with first-class callables in pipelines: `|> collect(...)`.
 *
 * @param iterable<array-key, mixed> $iterable
 * @return array<array-key, mixed>
 */
function collect(iterable $iterable) : array {
    return is_array($iterable) ? $iterable : iterator_to_array($iterable, true);
}

/**
 * Return unary callable for explode
 *
 * @param non-empty-string $separator
 * @param int $limit
 * @return Closure(string): list<string>
 */
function explode(string $separator, int $limit = PHP_INT_MAX) : Closure {
    return function (string $string) use ($separator, $limit) : array {
        return \explode($separator, $string, $limit);
    };
}

/**
 * Returns a predicate: $x === $value
 *
 * @param mixed $value
 * @return Closure(mixed) : bool
 */
function equals(mixed $value)  : Closure {
    return function (mixed $x) use ($value) : bool {
        return $x === $value;
    };
}

/**
 * Apply $then($value) when $predicate($value) is true; otherwise apply $else($value).
 *
 * Note: for constant branches, use value(...), e.g.
 *   $x |> if_else(is_int(...), value('int'), value('other'))
 *
 * @param callable $predicate
 * @param callable $then
 * @param callable $else
 * @return Closure(mixed) : mixed
 */
function if_else(callable $predicate, callable $then, callable $else) : Closure {
    return static function (mixed $value) use ($predicate, $then, $else) : mixed {
        return $predicate($value) === true ? $then($value) : $else($value);
    };
}

/**
 * Return unary callable for implode
 *
 * @return Closure(array<array-key, string>): string
 */
function implode(string $separator = "") : Closure {
    return function (array $array) use ($separator) : string {
        return \implode($separator, $array);
    };
}

/**
 * Return unary callable that increments by $by (default 1).
 * @return Closure(int|float): (int|float)
 */
function increment(int|float $by = 1) : Closure {
    return function (int|float $number) use ($by) : int|float {
        return $number + $by;
    };
}

/**
 * Return true if all items in an iterable match the predicate (or are true if predicate is null).
 * Comparison is strict.
 *
 * Short-circuits: stops iterating as soon as false (or actually !== true) is found.
 *
 * @param callable|null $callback
 * @return Closure(iterable<array-key, mixed>) : bool
 */
function iterable_all(?callable $callback = null) : Closure {
    return static function (iterable $iterable) use ($callback) : bool {

        if ($callback === null) {
            foreach ($iterable as $value) {
                if ($value !== true) {
                    return false;
                }
            }
        }

        else {
            foreach ($iterable as $value) {
                if ($callback($value) !== true) {
                    return false;
                }
            }
        }

        return true;
    };
}

/**
 * Return true if any item in an iterable matches the predicate (or is true if predicate is null).
 * Comparison is strict.
 *
 * Short-circuits: stops iterating as soon as a match is found.
 *
 * @param callable|null $callback
 * @return Closure(iterable<array-key, mixed>) : bool
 */
function iterable_any(?callable $callback = null) : Closure {
    return static function (iterable $iterable) use ($callback) : bool {

        if ($callback === null) {
            foreach ($iterable as $value) {
                if ($value === true) {
                    return true;
                }
            }
        }

        else {
            foreach ($iterable as $value) {
                if ($callback($value) === true) {
                    return true;
                }
            }
        }

        return false;
    };
}

/**
 * Return unary callable for filtering over an iterable
 *
 * @param callable $callback
 * @return Closure(iterable<array-key, mixed>) : Generator
 */
function iterable_filter(callable $callback) : Closure {
    return function (iterable $iterable) use ($callback) : Generator {
        foreach ($iterable as $key => $value) {
            if ($callback($value, $key)) {
                yield $key => $value;
            }
        }
    };
}

/**
 * Return the first value of an iterable (or null if empty).
 *
 * Warning: for Generators/Iterators, this consumes one element.
 *
 * @template TValue
 * @param iterable<array-key, TValue> $iterable
 * @return TValue|null
 */
function iterable_first(iterable $iterable): mixed {
    foreach ($iterable as $value) {
        return $value;
    }
    return null;
}

/**
 * Return unary callable for mapping over an iterable
 *
 * @param callable $callback
 * @return Closure(iterable<array-key, mixed>) : Generator
 */
function iterable_map(callable $callback) : Closure {
    return function (iterable $iterable) use ($callback) : Generator {
        foreach ($iterable as $key => $value) {
            yield $key => $callback($value);
        }
    };
}

/**
 * Return unary callable that returns the nth element (0-based) from an iterable.
 * Returns 0 if n is out of bounds
 *
 * @param int $n 0-based index
 * @return Closure(iterable<array-key, mixed>) : (mixed|null)
 * @throws InvalidArgumentException if $n < 0
 */
function iterable_nth(int $n): \Closure
{
    if ($n < 0) {
        throw new InvalidArgumentException("iterable_nth: n must be >= 0, got {$n}");
    }

    return static function (iterable $it) use ($n) {
        $i = 0;

        foreach ($it as $value) {
            if ($i === $n) {
                return $value;
            }
            $i++;
        }

        return null;
    };
}


/**
 * Return unary callable for reducing an iterable to a single value
 *
 * @param callable(TCarry|null, mixed, mixed) : (TCarry|null) $callback
 * @param TCarry|null $initial
 * @return Closure(iterable<array-key, mixed>) : (TCarry|null)
 *
 * @template TCarry
 */
function iterable_reduce(callable $callback, mixed $initial = null) : Closure {
    return function (iterable $iterable) use ($callback, $initial) : mixed {
        $carry = $initial;
        foreach ($iterable as $key => $value) {
            $carry = $callback($carry, $value, $key);
        }
        return $carry;
    };
}

/**
 * Lazily iterate over a string as bytes or byte-chunks of the provided size.
 *
 * @return Closure(string) : Generator<int, string>
 */
function iterable_string(int $size = 1) : Closure {
    if ($size < 1) {
        throw new InvalidArgumentException('size must be >= 1');
    }

    return static function (string $string) use ($size) : Generator {

        $length = \strlen($string);

        if ($size === 1) {
            for ($i = 0; $i < $length; $i++) {
                yield $i => $string[$i];
            }
        }

        else {
            for ($i = 0; $i < $length; $i += $size) {
                yield $i => \substr($string, $i, $size);
            }
        }
    };
}



/**
 * Return unary callable for taking $count items from an iterable
 *
 * @param int<0, max> $count
 * @return Closure(iterable<array-key, mixed>): Generator<array-key, mixed>
 */
function iterable_take(int $count) : Closure {
    return static function (iterable $iterable) use ($count) : Generator {
        if ($count <= 0) {
            return;
        }

        $i = 0;
        foreach ($iterable as $key => $value) {
            yield $key => $value;

            $i++;
            if ($i >= $count) {
                break;
            }
        }
    };
}


/**
 * Return iterable ticker
 *
 * @return Generator<int, int>
 */
function iterable_ticker(int $start = 0) : Generator {
    for ($i = $start; ; $i++) {
        yield $i;
    }
}

/**
 * Return unary callable for yielding windows of $size items from an iterable as an array.
 * Full windows only. Input keys are ignored.
 *
 * If $circular is true, windows are treated as circular: after reaching the end,
 *
 * @param int $size The desired window size (>0)
 * @param bool $circular Whether to yield circular (wraparound) windows
 * @return Closure(iterable<array-key, mixed>): Generator<int, list<mixed>>
 */
function iterable_window(int $size, bool $circular = false) : Closure {

    if ($size <= 0) {
        throw new InvalidArgumentException('$size must be > 0');
    }

    return static function (iterable $iterable) use ($size, $circular) : Generator {

        $buffer = [];

        foreach ($iterable as $value) {

            $buffer[] = $value;

            if (\count($buffer) === $size) {

                if ($circular === true) {
                    $prefix ??= \array_slice($buffer, 0, $size - 1);
                }

                yield $buffer;
                $buffer = \array_slice($buffer, 1);
            }
        }

        if ($circular === true && isset($prefix)) {

            $wrapped = array_merge($buffer, $prefix)
                |> iterable_window($size);

            foreach ($wrapped as $window) {
                yield $window;
            }
        }
    };
}

/**
 *  Lazily zip the left iterable with one or more right iterables.
 *
 *  Yields arrays (tuples) of values:
 *    [leftValue, right1Value, right2Value, ...]
 *
 *  - Preserves keys from the left iterable.
 *  - Stops when the left ends or when any right iterable is exhausted (shortest wins).
 *  - With zero right iterables, yields single-element tuples: [leftValue].
 *
 *  Note: Right iterables are consumed; if you pass rewindable Iterators, they are rewound at start.
 *
 * @param iterable<array-key, mixed> ...$right
 * @return Closure(iterable<array-key, mixed>) : iterable<array-key, array<int, mixed>>
 */
function iterable_zip(iterable ...$right) : \Closure {

    $mapper = static function (iterable $iterable): \Iterator {

        if (is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }

        if ($iterable instanceof \Iterator) {
            return $iterable;

        }

        return new \IteratorIterator($iterable);
    };

    return static function(iterable $left) use ($right, $mapper) : \Generator {

        $right = \array_map($mapper, $right);

        foreach ($right as $iterator) {
            $iterator->rewind();
        }

        foreach ($left as $key => $value) {

            if (\array_any($right, fn($iterator) => !$iterator->valid())) {
                return;
            }

            $buffer = [$value];

            foreach ($right as $iterator) {
                $buffer[] = $iterator->current();
            }

            yield $key => $buffer;

            foreach ($right as $iterator) {
                $iterator->next();
            }
        }
    };
}

/**
 * Lazily generate an (infinite) sequence by repeatedly applying a callback.
 *
 * If $include_seed is true (default), yields: seed, f(seed), f(f(seed)), ...
 * If false, yields: f(seed), f(f(seed)), ...
 *
 * @template T
 * @param callable(T) : T $callback
 * @return Closure(T) : Generator<int, T>
 */
function iterate(callable $callback, bool $include_seed = true) : Closure
{
    return static function (mixed $value) use ($callback, $include_seed) : Generator {
        if ($include_seed) {
            yield $value;
        }

        for (;;) {
            $value = $callback($value);
            yield $value;
        }
    };
}

/**
 * Return unary callable for preg_replace
 *
 * @template TFlags of 0|256|512|768
 * @template TFlags of int
 * @param string $pattern
 * @param TFlags $flags
 * @param int $offset
 * @return Closure(string): array<array{string|null, int<-1, max>}|string|null>
 */
function preg_match(string $pattern, int $flags = 0, int $offset = 0): Closure {
    return function (string $subject) use ($pattern, $flags, $offset) {
        \preg_match($pattern, $subject, $matches, $flags, $offset);
        return $matches;
    };
}

/**
 * @template TFlags of int
 * @param string $pattern
 * @param TFlags $flags
 * @param int $offset
 * @return Closure(string) : array<array-key, mixed>
 */
function preg_match_all(string $pattern, int $flags = 0, int $offset = 0): Closure {
    return function (string $subject) use ($pattern, $flags, $offset) {
        \preg_match_all($pattern, $subject, $matches, $flags, $offset);
        return $matches;
    };
}


/**
 * Return unary callable for preg_replace
 * $count is ignored
 *
 * @param string|array<string> $pattern
 * @param string|array<string> $replacement
 * @param int $limit
 * @return Closure(array<float|int|string>|string) : (array<string>|string|null)
 */
function preg_replace(string|array $pattern, string|array $replacement, int $limit = -1) : Closure {
    return function (string|array $subject) use ($pattern, $replacement, $limit) : string|array|null {
        /** @var array<float|int|string>|string $subject */
        return \preg_replace($pattern, $replacement, $subject, $limit);
    };
}

/**
 * Return unary callable for rsort
 *
 * @param int $flags
 * @return Closure(array<array-key, mixed>): list<mixed>
 */
function rsort(int $flags = SORT_REGULAR) : Closure {
    return function (array $array) use ($flags) : array {
        \rsort($array, $flags);
        return $array;
    };
}

/**
 * Return unary callable for sort
 *
 * @param int $flags
 * @return Closure(array<array-key, mixed>): list<mixed>
 */
function sort(int $flags = SORT_REGULAR) : Closure {
    return function (array $array) use ($flags) : array {
        \sort($array, $flags);
        return $array;
    };
}

/**
 * Return unary callable for str_replace
 * @param string|array<string> $search
 * @param string|array<string> $replace
 * @return Closure(array<string>|string): (string|array<string>)
 */
function str_replace(string|array $search, string|array $replace) : Closure {
    return function (string|array $subject) use ($search, $replace) : string|array {
        /** @var array<string>|string $subject */
        return \str_replace($search, $replace, $subject);
    };
}

/**
 * Return unary callable for checking if a string starts with a prefix.
 *
 * @param string $prefix
 * @return Closure(string): bool
 */
function str_starts_with(string $prefix) : Closure {
    return function (string $haystack) use ($prefix) : bool {
        return \str_starts_with($haystack, $prefix);
    };
}

/**
 * Apply $callback only when $predicate($value) !== true; otherwise return $value unchanged.
 * Strict comparison
 *
 * Example:
 *   $s |> unless(is_string(...), trim(...))
 *
 * @param callable $predicate
 * @param callable $callback
 * @return Closure(mixed) : mixed
 */
function unless(callable $predicate, callable $callback) : Closure {
    return static function (mixed $value) use ($predicate, $callback) : mixed {
        return $predicate($value) !== true ? $callback($value) : $value;
    };
}

/**
 * Return unary callable for usort
 * Reindexes keys
 *
 * @param callable(mixed, mixed): int $callback
 * @return Closure(array<array-key, mixed>): list<mixed>
 */
function usort(callable $callback) : Closure {
    return function (array $array) use ($callback) : array {
        \usort($array, $callback);
        return $array;
    };
}

/**
 * Return unary callable for uasort
 * Preserves keys
 *
 * @param callable(mixed, mixed): int $callback
 * @return Closure(array<array-key, mixed>): array<array-key, mixed>
 */
function uasort(callable $callback) : Closure {
    return function (array $array) use ($callback) : array {
        \uasort($array, $callback);
        return $array;
    };
}

/**
 * Returns a constant function: fn($_) => $value
 *
 * @template T
 * @param T $value
 * @return Closure(mixed) : T
 */
function value(mixed $value) : Closure {
    return function ($_) use ($value) {
        return $value;
    };
}

/**
 * Return unary callable for dumping the value in a pipe
 *
 * @return Closure(mixed) : mixed
 */
function var_dump() : Closure {
    return function (mixed $value) : mixed {
        \var_dump($value);
        return $value;
    };
}

/**
 * Apply $callback only when $predicate($value) is true; otherwise return $value unchanged.
 *
 * Example:
 *   $s |> when(is_string(...), trim(...))
 *
 * @param callable $predicate
 * @param callable $callback
 * @return Closure(mixed) : mixed
 */
function when(callable $predicate, callable $callback) : Closure {
    return static function (mixed $value) use ($predicate, $callback) : mixed {
        return $predicate($value) === true ? $callback($value) : $value;
    };
}

/**
 * Return unary callable for mapping over multiple arrays (zip semantics).
 *
 * @param (callable(): mixed)|null $callback
 * @return Closure(array<array<array-key, mixed>>): array<array-key, mixed>
 */
function zip_map(?callable $callback) : Closure {
    return function (array $arrays) use ($callback) : array {
        /** @var array<array<array-key, mixed>> $arrays */
        return $arrays === [] ? [] : \array_map($callback, ...$arrays);
    };
}

/**
 * Return unary callable for negating the boolean result of the given callback
 *
 * @param callable(mixed): bool $callback
 * @return Closure(mixed): bool
 */
function not(callable $callback) : Closure {
    return static function ($value) use ($callback) : bool {
        return true !== $callback($value);
    };
}
