<?php

declare(strict_types=1);

namespace Anarchitecture\pipe;

use Closure;
use Generator;

/**
 * Return unary callable for array_any
 *
 * @template TKey of array-key
 * @template TValue
 * @param callable(TValue, TKey): bool $callback
 * @return Closure(array<TKey, TValue>): bool
 */
function array_any(callable $callback) : Closure {
    return function (array $array) use ($callback) : bool {
        return \array_any($array, $callback);
    };
}

/**
 * Return unary callable for array_all
 *
 * @template TKey of array-key
 * @template TValue
 * @param callable(TValue, TKey): bool $callback
 * @return Closure(array<TKey, TValue>): bool
 */
function array_all(callable $callback) : Closure {
    return function (array $array) use ($callback) : bool {
        return \array_all($array, $callback);
    };
}

/**
 * Return unary callable for array_chunk
 *
 * @template TValue
 * @param int<1, max> $length
 * @param bool $preserve_keys
 * @return Closure(array<array-key, TValue>): list<array<array-key, TValue>>
 */
function array_chunk(int $length, bool $preserve_keys = false) : Closure {
    return function (array $array) use ($length, $preserve_keys) : array {
        return \array_chunk($array, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for array_filter
 *
 * @template TKey of array-key
 * @template TValue
 * @param callable<TValue, TKey>|null $callback
 * @param int $mode
 * @return Closure(array<TKey, TValue>): array<TKey, TValue>
 */
function array_filter(?callable $callback = null, int $mode = 0) : Closure {
    return function (array $array) use ($callback, $mode) : array {
        return \array_filter($array, $callback, $mode);
    };
}

/**
 * Return unary callable for array_map
 *
 * @template TKey of array-key
 * @template TIn
 * @template TOut
 * @param callable(TIn): TOut $callback
 * @return Closure(array<TKey, TIn>): array<TKey, TOut>
 */
function array_map(callable $callback) : Closure {
    return function (array $array) use ($callback) : array {
        return \array_map($callback, $array);
    };
}

/**
 * Return unary callable for returning the nth element of an array
 * @template T
 * @return Closure(array<array-key, T>): (T|null)
 */
function array_nth(int $i) : Closure {
    return function (array $array) use ($i) : mixed {
        return $array
            |> array_slice($i, 1)
            |> array_first(...);
    };
}


/**
 * Return unary callable for array_reduce
 *
 * @template TCarry
 * @param callable(TCarry, mixed): TCarry $callback
 * @param TCarry $initial
 * @return Closure(array<array-key, mixed>): TCarry
 */
function array_reduce(callable $callback, mixed $initial = null) : Closure {
    return function (array $array) use ($callback, $initial) : mixed {
        return \array_reduce($array, $callback, $initial);
    };
}

/**
 * Return unary callable for reducing an array until $until returns true.
 * Returns: [$carry, $key, $value] or [$carry, null, null] if never triggered.
 *
 * @template TCarry
 * @template TValue
 * @template TKey of array-key
 * @param callable(TCarry, TValue, TKey): TCarry $callback
 * @param callable(TCarry, TValue, TKey): bool $until
 * @param TCarry $initial
 * @return Closure(array<TKey, TValue>): array{0:TCarry, 1:TKey|null, 2:TValue|null}
 */
function array_reduce_until(callable $callback, callable $until, mixed $initial = null) : Closure {
    return function (array $array) use ($callback, $until, $initial) : array {
        $carry = $initial;

        foreach ($array as $key => $value) {
            $carry = $callback($carry, $value, $key);

            if ($until($carry, $value, $key)) {
                return [$carry, $key, $value];
            }
        }

        return [$carry, null, null];
    };
}

/**
 * Return unary callable for array_slice
 *
 * @template TValue
 * @param int $offset
 * @param int|null $length
 * @param bool $preserve_keys
 * @return Closure(array<array-key, TValue>): array<array-key, TValue>
 */
function array_slice(int $offset, ?int $length = null, bool $preserve_keys = false) : Closure {
    return function (array $array) use ($offset, $length, $preserve_keys) : array {
        return \array_slice($array, $offset, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for array_unique
 *
 * @template TKey of array-key
 * @template TValue
 * @return Closure(array<TKey, TValue>): array<TKey, TValue>
 */
function array_unique(int $flags = SORT_STRING) : Closure {
    return function (array $array) use ($flags) : array {
        return \array_unique($array, $flags);
    };
}

/**
 * Return unary callable for explode
 *
 * @return Closure(string): list<string>
 */
function explode(string $separator, int $limit = PHP_INT_MAX) : Closure {
    return function (string $string) use ($separator, $limit) : array {
        return \explode($separator, $string, $limit);
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
 * Return unary callable for filtering over an iterable
 *
 * @template TKey of array-key
 * @template TValue
 * @param callable(TValue, TKey): bool $callback
 * @return Closure(iterable<TKey, TValue>): Generator<TKey, TValue>
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
 * Return unary callable for taking $count items from an iterable
 *
 * @template TKey of array-key
 * @template TValue
 * @param int<0, max> $count
 * @return Closure(iterable<TKey, TValue>): Generator<TKey, TValue>
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
 * Return unary callable for preg_replace
 * $count is ignored
 *
 * @template TSubject of string|array
 * @param string|array $pattern
 * @param string|array $replacement
 * @param int $limit
 * @return Closure(TSubject): (TSubject|null)
 */
function preg_replace(string|array $pattern, string|array $replacement, int $limit = -1) : Closure {
    return function (string|array $subject) use ($pattern, $replacement, $limit) : string|array|null {
        return \preg_replace($pattern, $replacement, $subject, $limit);
    };
}

/**
 * Return unary callable for rsort
 * @template T
 * @param int $flags
 * @return Closure(array<array-key, T>): list<T>
 */
function rsort(int $flags = SORT_REGULAR) : Closure {
    return function (array $array) use ($flags) : array {
        \rsort($array, $flags);
        return $array;
    };
}

/**
 * Return unary callable for sort
 * @template T
 * @param int $flags
 * @return Closure(array<array-key, T>): list<T>
 */
function sort(int $flags = SORT_REGULAR) : Closure {
    return function (array $array) use ($flags) : array {
        \sort($array, $flags);
        return $array;
    };
}

/**
 * Return unary callable for str_replace
 * @template TSubject of string|array
 * @return Closure(TSubject): TSubject
 */
function str_replace(string|array $search, string|array $replace) : Closure {
    return function (string|array $subject) use ($search, $replace) : string|array {
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
 * Return unary callable for usort
 *
 * @template T
 * @param callable(T, T): int $callback
 * @return Closure(array<array-key, T>): list<T>
 */
function usort(callable $callback) : Closure {
    return function (array $array) use ($callback) : array {
        \usort($array, $callback);
        return $array;
    };
}

/**
 * Return unary callable for dumping the value in a pipe
 *
 * @template T
 * @return Closure(T): T
 */
function var_dump() : Closure {
    return function (mixed $value) : mixed {
        \var_dump($value);
        return $value;
    };
}

/**
 * Return unary callable for mapping over multiple arrays (zip semantics).
 *
 * @template TResult
 * @param callable(mixed...): TResult $callback
 * @return Closure(list<array>): list<TResult>
 */
function zip_map(callable $callback) : Closure {
    return function (array $arrays) use ($callback) : array {
        return $arrays === [] ? [] : \array_map($callback, ...$arrays);
    };
}