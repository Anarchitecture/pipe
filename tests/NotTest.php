<?php

declare(strict_types=1);

namespace Anarchitecture\pipe\Tests;

use PHPUnit\Framework\TestCase;

use function Anarchitecture\pipe\not;

final class NotTest extends TestCase
{
    public function test_that_it_correctly_inverts_the_result(): void
    {
        $notNull = not(is_null(...));

        self::assertTrue($notNull(3));
        self::assertTrue($notNull("test"));
        self::assertTrue($notNull(new \stdClass()));
        self::assertFalse($notNull(null));
    }
}
