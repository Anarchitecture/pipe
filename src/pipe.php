<?php

declare(strict_types=1);

namespace Anarchitecture\pipe;

/**
 * Return unary callable for array_any
 */
function array_any(callable $callback) : callable {
    return function (array $array) use ($callback) : bool {
        return \array_any($array, $callback);
    };
}

/**
 * Return unary callable for array_all
 */
function array_all(callable $callback) : callable {
    return function (array $array) use ($callback) : bool {
        return \array_all($array, $callback);
    };
}

/**
 * Return unary callable for array_chunk
 */
function array_chunk(int $length, bool $preserve_keys = false) : callable {
    return function (array $array) use ($length, $preserve_keys) : array {
        return \array_chunk($array, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for array_filter
 */
function array_filter(?callable $callback = null, int $mode = 0) : callable {
    return function (array $array) use ($callback, $mode) : array {
        return \array_filter($array, $callback, $mode);
    };
}

/**
 * Return unary callable for array_map
 */
function array_map(callable $callback) : callable {
    return function (array $array) use ($callback) : array {
        return \array_map($callback, $array);
    };
}

/**
 * Return unary callable for array_reduce
 */
function array_reduce(callable $callback, mixed $initial = null) : callable {
    return function (array $array) use ($callback, $initial) : mixed {
        return \array_reduce($array, $callback, $initial);
    };
}

/**
 * Return unary callable for array_slice
 */
function array_slice(int $offset, ?int $length = null, bool $preserve_keys = false) : callable {
    return function (array $array) use ($offset, $length, $preserve_keys) : array {
        return \array_slice($array, $offset, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for explode
 */
function explode(string $separator, int $limit = PHP_INT_MAX) : callable {
    return function (string $string) use ($separator, $limit) : false|array {
        return \explode($separator, $string, $limit);
    };
}

/**
 * Return unary callable for rsort
 */
function rsort(int $flags = SORT_REGULAR) : callable {
    return function (array $array) use ($flags) : array {
        \rsort($array, $flags);
        return $array;
    };
}

/**
 * Return unary callable for rsort
 */
function sort(int $flags = SORT_REGULAR) : callable {
    return function (array $array) use ($flags) : array {
        \sort($array, $flags);
        return $array;
    };
}

/**
 * Return unary callable for usort
 */
function usort(callable $callback) : callable {
    return function (array $array) use ($callback) : array {
        \usort($array, $callback);
        return $array;
    };
}

/**
 * Return unary callable for mapping over multiple arrays (zip semantics).
 */
function zip_map(callable $callback) : callable {
    return function (array $arrays) use ($callback) : array {
        return \array_map($callback, ...$arrays);
    };
}