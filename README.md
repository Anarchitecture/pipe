# pipe

`pipe` is a small PHP library that provides functions returning **unary callables**,
designed to be used with the **PHP 8.5 pipe operator**.

The goal is to make it easy to **leverage existing functions in pipe expressions**
without having to write inline closures.

## Usage
```php
use Anarchitecture\pipe as p;

$a = range(1, 8)
    |> p\array_map(fn ($x) => $x ** $x)
    |> p\array_chunk(4)
    |> p\array_map(array_sum(...));

// $a === [288, 17650540]
```


Each helper returns a unary callable that can be composed in a pipe:

```php
p\array_map(fn ($x) => $x * 2);
// fn (array $input) => array_map(fn ($x) => $x * 2, $input)
```

## Semantics

A few helpers intentionally differ from the underlying PHP function behavior to make pipelines pleasant:

- `p\sort()`, `p\rsort()`, `p\usort()` **return the sorted array** (instead of the native `true`).
- `p\zip_map($callback)([])` returns `[]` (avoids calling `array_map()` with no arrays).
- `p\var_dump()` is a “tap”: it dumps the value and returns it unchanged.

## Example
This example uses `pipe` library functions to solve [Advent of Code 2025 Day 9 Part 1](https://adventofcode.com/2025/day/9):
```php
use Anarchitecture\pipe as p;

function rectangles(array $tiles) : array {
    $rectangles = [];
    while ($current = array_pop($tiles)) {
        foreach ($tiles as $tile) {
            $rectangles[] = [$current, $tile];
        }
    }
    return $rectangles;
}

function area(?array $tiles = null) : int {
    return $tiles
        |> p\zip_map(fn ($da, $db) => abs($da - $db) + 1)
        |> array_product(...);
}

$largest_rectangle = file_get_contents('input')
    |> p\explode(PHP_EOL)
    |> p\array_map(p\explode(','))
    |> rectangles(...)
    |> p\array_map(area(...))
    |> p\rsort()
    |> array_first(...);

echo $largest_rectangle . PHP_EOL;
```

## Philosophy
* functions return unary callables
* explicitly designed for pipe expressions
* thin wrappers around existing PHP functions
* small, predictable, and composable
* no magic, just functions

## Status

Experimental — based on PHP 8.5 pipes.

## License

MIT