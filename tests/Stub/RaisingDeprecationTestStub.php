<?php

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

class RaisingDeprecationTestStub extends TestCase
{
    const DEPRECATION_MESSAGE = 'This "Foo" method is deprecated';

    /**
     * @dataProvider multirunDataprovider
     */
    public function testDeprecation()
    {
        $this->assertTrue(true, 'This avoids the risky status');
        @trigger_error(self::DEPRECATION_MESSAGE, E_USER_DEPRECATED);
    }

    public function multirunDataprovider()
    {
        return [
            [],
            [],
            [],
        ];
    }
}
