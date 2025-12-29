<?php

declare(strict_types=1);

namespace Anarchitecture\pipe;

/**
 * Return unary callable for array_any
 */
function array_any(callable $c) : callable {
    return function (array $a) use ($c) : bool {
        return \array_any($a, $c);
    };
}

/**
 * Return unary callable for array_all
 */
function array_all(callable $c) : callable {
    return function (array $a) use ($c) : bool {
        return \array_all($a, $c);
    };
}

/**
 * Return unary callable for array_chunk
 */
function array_chunk(int $length, bool $preserve_keys = false) : callable {
    return function (array $a) use ($length, $preserve_keys) : array {
        return \array_chunk($a, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for array_map
 */
function array_map(callable $c) : callable {
    return function (array $a) use ($c) : array {
        return \array_map($c, $a);
    };
}

/**
 * Return unary callable for array_slice
 */
function array_slice(int $offset, ?int $length = null, bool $preserve_keys = false) : callable {
    return function (array $a) use ($offset, $length, $preserve_keys) : array {
        return \array_slice($a, $offset, $length, $preserve_keys);
    };
}

/**
 * Return unary callable for explode
 */
function explode(string $d) : callable {
    return function (string $s) use ($d) : array {
        return \explode($d, $s);
    };
}

/**
 * Return unary callable for usort
 */
function usort(callable $c) : callable {
    return function (array $a) use ($c) : array {
        \usort($a, $c);
        return $a;
    };
}