<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

class RaisingDeprecationTestStub extends TestCase
{
    final public const DEPRECATION_MESSAGE = 'This "Foo" method is deprecated';

    /**
     * @dataProvider multirunDataprovider
     */
    public function testDeprecation(): void
    {
        $this->assertTrue(true, 'This avoids the risky status');
        @trigger_error(self::DEPRECATION_MESSAGE, E_USER_DEPRECATED);
    }

    public static function multirunDataprovider(): array
    {
        return [
            [],
            [],
            [],
        ];
    }
}
