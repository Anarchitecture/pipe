# anarchitecture/pipe

`anarchitecture/pipe` is a small PHP library that provides functions returning **unary callables**,
designed to be used with the **PHP 8.5 pipe operator** (`|>`).

The goal is to make it easy to **leverage existing functions in pipe expressions** without having to write inline closures.

## Requirements
- PHP **8.5+** (pipe operator support)

## Installation
```bash
composer require anarchitecture/pipe
```

## Quick Start
```php
use Anarchitecture\pipe as p;

$a = range(1, 8)
    |> p\array_map(fn ($x) => $x ** $x)
    |> p\array_chunk(4)
    |> p\array_map(array_sum(...));

// [288, 17650540]
```

### What a helper returns

Each helper provides a **unary callable**:

```php
use Anarchitecture\pipe as p;

p\array_map(fn ($x) => $x * 2);
// fn (array $input) => array_map(fn ($x) => $x * 2, $input)
```

## Helpers (by category)

### Arrays
- `p\array_all(callable $callback)`
- `p\array_any(callable $callback)`
- `p\array_chunk(int $length, bool $preserve_keys = false)`
- `p\array_filter(callable $callback, int $mode = 0)`
- `p\array_flatten(array $arrays)` — flattens one level (array of arrays to single array)
- `p\array_map(callable $mapper)`
- `p\array_nth(int $i)` — nth element or `null`
- `p\array_reduce(callable $reducer, mixed $initial = null)`
- `p\array_reduce_until(callable $reducer, callable $until, mixed $initial = null)`
- `p\array_slice(int $offset, ?int $length = null, bool $preserve_keys = false)`
- `p\array_sum(callable $callback)` — map each element over $callback then sum numeric results
- `p\array_unique(int $flags = SORT_STRING)`
- `p\sort(int $flags = SORT_REGULAR)`
- `p\rsort(int $flags = SORT_REGULAR)`
- `p\array_transpose()` — transpose a 2D array (matrix)
- `p\usort(callable $comparator)`

### Strings / regex
- `p\explode(string $separator, int $limit = PHP_INT_MAX)`
- `p\implode(string $separator = "")`
- `p\str_replace(string|array $search, string|array $replace)`
- `p\str_starts_with(string $prefix)`
- `p\preg_match(string $pattern, int $flags = 0, int $offset = 0)` — returns the `$matches` array (empty array when no match)
- `p\preg_match_all(string $pattern, int $flags = 0, int $offset = 0)` — returns the `$matches` array (empty array when no match)
- `p\preg_replace(string|array $pattern, string|array $replacement, int $limit = -1)`

### Iterables (Generators-friendly)
- `p\iterable_allocate(int $total)` — yields all non-negative integer allocations of `$total` across the input iterable (preserves keys; `$total < 0` throws)
- `p\iterable_any(?callable $callback = null)` — returns `true` if any item matches (or is `=== true` when callback is `null`); short-circuits
- `p\iterable_filter(callable $callback)` — yields matching items
- `p\iterable_map(callable $callback)` — yields mapped items
- `p\iterable_permutation(array $array)` — yields all permutations of the input array
- `p\iterable_reduce(callable $callback, $initial = null)` — reduces an iterable to a single value
- `p\iterable_string(int $size = 1)` — lazily iterate over a string as **bytes** (`$size = 1`) or **byte-chunks** (`$size > 1`).
- `p\iterable_take(int $count)` — yields first `$count` items
- `p\iterable_first(iterable $iterable)` — returns first item or `null` (**consumes one element**)
- `p\iterable_ticker(int $start = 0)` — infinite counter generator
- `p\iterable_window(int $size, bool $circular = false)` – sliding windows over iterables (optionally circular)
- `p\iterable_zip(iterable ...$right)` — lazily zips the left iterable with one or more right iterables; yields tuples and stops at the shortest (preserves left keys)
- `p\iterate(callable $callback, bool $include_seed = true)` — infinite sequence by repeated application (yields seed first by default)

### Control flow
- `p\when(callable $predicate, callable $callback)` — applies `$callback` only when `$predicate($value) === true` (otherwise returns the input unchanged)
- `p\if_else(callable $predicate, callable $then, callable $else)` — applies `$then($value)` when `$predicate($value) === true`, otherwise `$else($value)`

### Predicates / functional
- `p\equals(mixed $value)` — returns true if `item === $value`
- `p\value(mixed $value)` — constant function returns `$value`

### Misc
- `p\apply(callable $callback)` — applies an array of arguments to a callable (numeric keys => positional, string keys => named; mixed keys rejected)
- `p\increment(int|float $by = 1)`
- `p\var_dump()` — “tap” debugging helper (returns value unchanged)
- `p\zip_map(?callable $callback)` — zip semantics over multiple arrays

## Semantics (intentional differences)

A few helpers differ from their underlying built-ins to make pipelines pleasant:
- `p\apply($callback)` rejects arrays with mixed numeric and string keys (to avoid PHP’s “positional after named” edge cases).
- `p\preg_match()` and `p\preg_match_all()` return the `$matches` array (like the third arg of `\preg_match()`), not the match count; no match => `[]`.
- `p\sort()`, `p\rsort()`, `p\usort()` **return the sorted array** (native functions return `true`/`false`).
- `p\zip_map($callback)([])` returns `[]` (avoids calling `array_map()` with no arrays).
- `p\var_dump()` is a “tap”: it dumps the value and returns it unchanged.

## Examples

### Apply (spread arguments)

```php
use Anarchitecture\pipe as p;

// numeric keys => positional
$out1 = [10 => "a", 20 => "b", 30 => "c"]
    |> p\apply(fn (string $a, string $b, string $c) => $a . $b . $c);

// "abc"

// string keys => named
$out2 = ["b" => 2, "a" => 1]
    |> p\apply(fn (int $a, int $b) => $a - $b);

// -1
```

### Conditional transform

```php
use Anarchitecture\pipe as p;

$out = "  Hello  "
    |> p\when(is_string(...), trim(...))
    |> p\when(p\equals("Hello"), p\value("bye"));

// "bye"
```

### If / else branching

```php
use Anarchitecture\pipe as p;

$out = "Hello"
    |> p\if_else(
        p\equals("Hello"),
        p\value("bye"),
        p\value("unknown")
    );

// "bye"
```

### Working with iterables (lazy pipelines)

```php
use Anarchitecture\pipe as p;

$result = 0
    |> p\iterate(static fn(int $x) : int => $x + 1)
    |> p\iterable_take(4)
    |> iterator_to_array(...);

// [0, 1, 2, 3]
```

### Zip-map (multiple arrays)

```php
use Anarchitecture\pipe as p;

$sumPairs = [[6, 7, 8], [10, 20, 30]]
    |> p\zip_map(fn ($a, $b) => $a + $b);

// [16, 27, 38]
```

### Array transpose (matrix)
```php
use Anarchitecture\pipe as p;

$matrix = [
    [1, 2, 3],
    [4, 5, 6],
];

$t = $matrix
    |> p\array_transpose();

// [
//  [1, 4],
//  [2, 5],
//  [3, 6]
//]
```

### Iterable zip

```php
use Anarchitecture\pipe as p;

$result = [1, 2]
    |> p\iterable_zip([10, 20], [100, 200])
    |> iterator_to_array(...);

// [
//  [1, 10, 100],
//  [2, 20, 200]
//]
```

### Sliding windows (iterables)

```php
use Anarchitecture\pipe as p;

// linear (default): full windows only, no wraparound
$linear = [1, 2, 3, 4, 5, 6]
    |> p\iterable_window(3)
    |> iterator_to_array(...);

// [
//   [1, 2, 3],
//   [2, 3, 4],
//   [3, 4, 5],
//   [4, 5, 6],
// ]

// circular: adds the boundary-crossing windows (end -> start)
$circular = [0, 1, 2, -2, -1]
    |> p\iterable_window(size: 4, circular: true)
    |> iterator_to_array(...);

// [
//   [0, 1, 2, -2],
//   [1, 2, -2, -1],
//   [2, -2, -1, 0],
//   [-2, -1, 0, 1],
//   [-1, 0, 1, 2],
// ]
```

### Allocate a total across items (integer compositions)

Generate all non-negative integer allocations that sum to a fixed total.

```php
use Anarchitecture\pipe as p;

$allocations = ['a' => null, 'b' => null, 'c' => null]
    |> p\iterable_allocate(2)
    |> iterator_to_array(...);

// [
//   ['a' => 0, 'b' => 0, 'c' => 2],
//   ['a' => 0, 'b' => 1, 'c' => 1],
//   ['a' => 0, 'b' => 2, 'c' => 0],
//   ['a' => 1, 'b' => 0, 'c' => 1],
//   ['a' => 1, 'b' => 1, 'c' => 0],
//   ['a' => 2, 'b' => 0, 'c' => 0],
// ]
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