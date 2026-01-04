<?php

declare(strict_types=1);

namespace Anarchitecture\pipe;

use Closure;
use Generator;

/**
 * Return unary callable for array_any
 * @return Closure(array): bool
 */
function array_any(callable $callback) : Closure {
    return function (array $array) use ($callback) : bool {
        return \array_any($array, $callback);
    };
}

/**
 * Return unary callable for array_all
 * @return Closure(array): bool
 */
function array_all(callable $callback) : Closure {
    return function (array $array) use ($callback) : bool {
        return \array_all($array, $callback);
    };
}

/**
 * Return unary callable for array_chunk
 * @template T
 * @return Closure(array<T>): array<array<T>>
 */
function array_chunk(int $length, bool $preserve_keys = false) : Closure {
    return function (array $array) use ($length, $preserve_keys) : array {
        return \array_chunk($array, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for array_filter
 * @template T
 * @return Closure(array<T>): array<T>
 */
function array_filter(?callable $callback = null, int $mode = 0) : Closure {
    return function (array $array) use ($callback, $mode) : array {
        return \array_filter($array, $callback, $mode);
    };
}

/**
 * Return unary callable for array_map
 * @return Closure(array): array
 */
function array_map(callable $callback) : Closure {
    return function (array $array) use ($callback) : array {
        return \array_map($callback, $array);
    };
}

/**
 * Return unary callable for returning the nth element of an array
 * @template T
 * @return Closure(array<T>): (T|null)
 */
function array_nth(int $i) : Closure {
    return function (array $array) use ($i) : mixed {
        return $array
            |> array_slice($i, 1)
            |> array_first(...)
            ?? null;
    };
}


/**
 * Return unary callable for array_reduce
 * @return Closure(array): mixed
 */
function array_reduce(callable $callback, mixed $initial = null) : Closure {
    return function (array $array) use ($callback, $initial) : mixed {
        return \array_reduce($array, $callback, $initial);
    };
}

/**
 * Return unary callable for reducing an array until $until returns true.
 * Returns: [$carry, $key, $value] or [$carry, null, null] if never triggered.
 * @return Closure(array): array
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
 * @return Closure(array): array
 */
function array_slice(int $offset, ?int $length = null, bool $preserve_keys = false) : Closure {
    return function (array $array) use ($offset, $length, $preserve_keys) : array {
        return \array_slice($array, $offset, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for array_unique
 * @return Closure(array): array
 */
function array_unique(int $flags = SORT_STRING) : Closure {
    return function (array $array) use ($flags) : array {
        return \array_unique($array, $flags);
    };
}

/**
 * Return unary callable for explode
 * @return Closure(string): array
 */
function explode(string $separator, int $limit = PHP_INT_MAX) : Closure {
    return function (string $string) use ($separator, $limit) : array {
        return \explode($separator, $string, $limit);
    };
}

/**
 * Return unary callable for implode
 * @return Closure(array): string
 */
function implode(string $separator = "") : Closure {
    return function (array $array) use ($separator) : string {
        return \implode($separator, $array);
    };
}

/**
 * Return unary callable that increments by $by (default 1).
 * @return Closure(int): int
 */
function increment(int $by = 1) : Closure {
    return function (int $number) use ($by) : int {
        return $number + $by;
    };
}

/**
 * Return unary callable that returns the current element of an iterable
 * @return Closure(iterable): mixed
 */
function iterable_current() : Closure {
    return function (iterable $iterable) : mixed {
        foreach ($iterable as $value) {
            return $value;
        }
        return null;
    };
}


/**
 * Return unary callable for filtering over an iterable
 * @return Closure(iterable): Generator
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
 * Return unary callable for taking $count items from an iterable
 * @return Closure(iterable): Generator
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
 * @return Generator<int>
 */
function iterable_ticker(int $start = 0) : Generator {
    for ($i = $start; ; $i++) {
        yield $i;
    }
}

/**
 * Return unary callable for preg_replace
 * @template T of string|array
 * @return Closure(T): (T|null)
 */
function preg_replace(string|array $pattern, string|array $replacement, int $limit = -1) : Closure {
    return function (string|array $subject) use ($pattern, $replacement, $limit) : string|array|null {
        return \preg_replace($pattern, $replacement, $subject, $limit);
    };
}

/**
 * Return unary callable for rsort
 * @template T
 * @return Closure(array<T>): list<T>
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
 * @return Closure(array<T>): list<T>
 */
function sort(int $flags = SORT_REGULAR) : Closure {
    return function (array $array) use ($flags) : array {
        \sort($array, $flags);
        return $array;
    };
}

/**
 * Return unary callable for str_replace
 * @template T of string|array
 * @return Closure(T): T
 */
function str_replace(string|array $search, string|array $replace) : Closure {
    return function (string|array $subject) use ($search, $replace) : string|array {
        return \str_replace($search, $replace, $subject);
    };
}

/**
 * Return unary callable for usort
 * @template T
 * @return Closure(array<T>): list<T>
 */
function usort(callable $callback) : Closure {
    return function (array $array) use ($callback) : array {
        \usort($array, $callback);
        return $array;
    };
}

/**
 * Return unary callable for dumping the value in a pipe
 * @return Closure(mixed): mixed
 */
function var_dump() : Closure {
    return function (mixed $value) : mixed {
        \var_dump($value);
        return $value;
    };
}

/**
 * Return unary callable for mapping over multiple arrays (zip semantics).
 * @return Closure(array): array
 */
function zip_map(callable $callback) : Closure {
    return function (array $arrays) use ($callback) : array {
        return $arrays === [] ? [] : \array_map($callback, ...$arrays);
    };
}