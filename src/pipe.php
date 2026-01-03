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
 * unary callable for returning the nth element of an array
 */
function array_nth(int $i) : callable {
    return function (array $array) use ($i) : mixed {
        return $array
            |> array_slice($i, 1)
            |> array_first(...)
            ?? null;
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
 * Reduce an array until $until returns true.
 * Returns: [$carry, $key, $value] or [$carry, null, null] if never triggered.
 */
function array_reduce_until(callable $callback, callable $until, mixed $initial = null) : callable {
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
    return function (string $string) use ($separator, $limit) : array {
        return \explode($separator, $string, $limit);
    };
}

/**
 * Return unary callable for implode
 */
function implode(string $separator = "") : callable {
    return function (array $array) use ($separator) : string {
        return \implode($separator, $array);
    };
}

/**
 * Return unary callable that increments by $by (default 1).
 */
function increment(int|float $by = 1) : callable {
    return function (int|float $number) use ($by) : int|float {
        return $number + $by;
    };
}

/**
 * Return unary callable for preg_replace
 */
function preg_replace(string|array $pattern, string|array $replacement, int $limit = -1) : callable {
    return function (string|array $subject) use ($pattern, $replacement, $limit) : string|array|null {
        return \preg_replace($pattern, $replacement, $subject, $limit);
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
 * Return unary callable for sort
 */
function sort(int $flags = SORT_REGULAR) : callable {
    return function (array $array) use ($flags) : array {
        \sort($array, $flags);
        return $array;
    };
}

/**
 * Return unary callable for str_replace
 */
function str_replace(string|array $search, string|array $replace) : callable {
    return function (string|array $subject) use ($search, $replace) : string|array {
        return \str_replace($search, $replace, $subject);
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
 * Return unary callable for dumping the value in a pipe
 */
function var_dump() : callable {
    return function (mixed $value) : mixed {
        \var_dump($value);
        return $value;
    };
}

/**
 * Return unary callable for mapping over multiple arrays (zip semantics).
 */
function zip_map(callable $callback) : callable {
    return function (array $arrays) use ($callback) : array {
        return $arrays === [] ? [] : \array_map($callback, ...$arrays);
    };
}