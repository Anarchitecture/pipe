<?php

namespace Anarchitecture\pipe\Tests;

use PHPUnit\Framework\TestCase;
use function Anarchitecture\pipe\collect;
use function Anarchitecture\pipe\iterable_flatten;
use function Anarchitecture\pipe\iterable_map;
use function Anarchitecture\pipe\iterable_take;

class IterableFlattenTest extends TestCase
{
    public function test_it_flattens_arrays_normally(): void {
        $arr = [
            [1,2,3],
            [4,5,6],
        ];

        self::assertSame(collect(iterable_flatten(preserve_keys: false)($arr)), [1,2,3,4,5,6]);
    }

    public function test_that_it_flattens_generators(): void {
        $createListOfGenerators = function () {
            return [
                 (function() {
                     yield 'a';
                     yield 'b';
                     yield 'c';
                 })(),
                 (function() {
                     yield 'x';
                     yield 'y';
                     yield 'z';
                 })(),
           ];
        };

        self::assertSame(collect(iterable_flatten()($createListOfGenerators())), ['x', 'y', 'z']);
        self::assertSame(collect(iterable_flatten(preserve_keys: false)($createListOfGenerators())), ['a', 'b', 'c', 'x', 'y', 'z']);
    }

    public function test_that_yields_nothing_for_empty_input(): void {
        $generator = iterable_flatten()([]);
        self::assertSame(iterator_to_array($generator), []);
    }

    public function test_that_it_executes_lazily(): void {

        $take_amount = 12;
        $result = [1,2,3,4,5]
            |> iterable_map(fn($i) => $i < 5 ? [1,2,3] : self::fail('this function should not be invoked this many times'))
            |> iterable_flatten()
            |> iterable_take($take_amount);

        // this should run without failing
        self::assertSame(count(iterator_to_array($result, preserve_keys: false)), $take_amount);
    }
}
