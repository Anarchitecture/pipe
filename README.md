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

Each helper provices a **unary callable**:

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
- `p\array_map(callable $mapper)`
- `p\array_nth(int $i)` — nth element or `null`
- `p\array_reduce(callable $reducer, mixed $initial = null)`
- `p\array_reduce_until(callable $reducer, callable $until, mixed $initial = null)`
- `p\array_slice(int $offset, ?int $length = null, bool $preserve_keys = false)`
- `p\array_unique(int $flags = SORT_STRING)`
- `p\sort(int $flags = SORT_REGULAR)`
- `p\rsort(int $flags = SORT_REGULAR)`
- `p\usort(callable $comparator)`

### Strings / regex
- `p\explode(string $separator, int $limit = PHP_INT_MAX)`
- `p\implode(string $separator = "")`
- `p\str_replace(string|array $search, string|array $replace)`
- `p\str_starts_with(string $prefix)`
- `p\preg_replace(string|array $pattern, string|array $replacement, int $limit = -1)`

### Iterables (Generators-friendly)
- `p\iterable_filter(callable $callback)` — yields matching items
- `p\iterable_map(callable $callback)` — yields mapped items
- `p\iterable_reduce(callable $callback, $initial = 0)` — reduces an iterable to a single value
- `p\iterable_take(int $count)` — yields first `$count` items
- `p\iterable_first(iterable $iterable)` — returns first item or `null` (**consumes one element**)
- `p\iterable_ticker(int $start = 0)` — infinite counter generator

### Misc
- `p\increment(int|float $by = 1)`
- `p\var_dump()` — “tap” debugging helper (returns value unchanged)
- `p\zip_map(?callable $callback)` — zip semantics over multiple arrays


## Semantics (intentional differences)

A few helpers differ from their underlying built-ins to make pipelines pleasant:

- `p\sort()`, `p\rsort()`, `p\usort()` **return the sorted array** (native functions return `true`/`false`).
- `p\zip_map($callback)([])` returns `[]` (avoids calling `array_map()` with no arrays).
- `p\var_dump()` is a “tap”: it dumps the value and returns it unchanged.

## Examples

### Tap-debugging in a pipeline

```php
use Anarchitecture\pipe as p;

$out = "  Hello  "
    |> trim(...)
    |> p\var_dump()
    |> strtoupper(...);

// HELLO
```

### Working with iterables (lazy pipelines)

```php
use Anarchitecture\pipe as p;

$values = p\iterable_ticker(1)
    |> p\iterable_map(fn ($x) => $x * $x)
    |> p\iterable_take(5)
    |> iterator_to_array(...);

// [1, 4, 9, 16, 25]
```

### Zip-map (multiple arrays)

```php
use Anarchitecture\pipe as p;

$sumPairs = [[6, 7, 8], [10, 20, 30]]
    |> p\zip_map(fn ($a, $b) => $a + $b);

// [16, 27, 38]
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