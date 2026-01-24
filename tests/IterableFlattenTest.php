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
        $stage = [
            [1,2,3],
            [4,5,6],
        ];

        $result = $stage
            |> iterable_flatten(preserve_keys: false)
            |> collect(...);

        self::assertSame($result, [1,2,3,4,5,6]);
    }

    public function test_that_it_flattens_generators() : void {

        $outer = static function () : \Generator {
            yield (static function () : \Generator { yield 'a'; yield 'b'; yield 'c'; })();
            yield (static function () : \Generator { yield 'x'; yield 'y'; yield 'z'; })();
        };

        $result_with_keys_preserved = $outer()
                |> iterable_flatten(preserve_keys: true)
                |> collect(...);

        $result_with_keys_not_preserved = $outer()
                |> iterable_flatten(preserve_keys: false)
                |> collect(...);

        self::assertSame(['x', 'y', 'z'], $result_with_keys_preserved);
        self::assertSame(['a', 'b', 'c', 'x', 'y', 'z'], $result_with_keys_not_preserved);
    }


    public function test_that_yields_nothing_for_empty_input(): void {

        $stage = [];

        $result = $stage
            |> iterable_flatten()
            |> collect(...);

        self::assertSame($result, []);
    }

    public function test_that_it_executes_lazily(): void {

        $take_amount = 12;
        $calls = 0;

        $callback = static function (int $i) use (&$calls) : array {
            $calls++;
            return [1, 2, 3];
        };

        $result = [1, 2, 3, 4, 5]
                |> iterable_map($callback)
                |> iterable_flatten(preserve_keys: false)
                |> iterable_take($take_amount)
                |> collect(...);

        self::assertSame([1,2,3,1,2,3,1,2,3,1,2,3], $result);
        self::assertSame(4, $calls); // 12 items / 3 per input = 4 inputs needed
    }

    public function test_is_lazy_and_does_not_advance_to_next_inner_iterable_if_not_needed() : void {

        $stage = (function () : \Generator {
            yield (function () : \Generator {
                yield 1;
                yield 2;
                yield 3;
            })();

            yield (function () : \Generator {
                self::fail('second inner iterable should not be iterated');
                /** @phpstan-ignore-next-line */
                yield 999;
            })();
        })();

        $result = $stage
            |> iterable_flatten(preserve_keys: false)
            |> iterable_take(2)
            |> collect(...);

        self::assertSame([1, 2], $result);
    }

    public function test_empty_inner_iterables_are_ignored() : void {

        $stage = [
            [],
            ['a' => 1],
            [],
            [2, 3],
        ];

        $result = $stage
                |> iterable_flatten(preserve_keys: false)
                |> collect(...);

        self::assertSame([1, 2, 3], $result);
    }

    public function test_flattens_one_level_with_keys_preserved_by_default() : void {

        $stage = [
            ['a' => 1, 0 => 'x'],
            ['a' => 2, 0 => 'y'],
        ];

        $result = $stage
            |> iterable_flatten()
            |> collect(...);

        self::assertSame([
            'a' => 2,
            0   => 'y',
        ], $result);
    }

    public function test_flattens_one_level_and_discards_keys_when_preserve_keys_is_false() : void {

        $stage = [
            ['a' => 1, 0 => 'x'],
            ['a' => 2, 0 => 'y'],
        ];

        $result = $stage
            |> iterable_flatten(preserve_keys: false)
            |> collect(...);

        self::assertSame([1, 'x', 2, 'y'], $result);
    }

}
