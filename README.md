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