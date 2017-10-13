<?php

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

class RaisingDeprecationTestStub extends TestCase
{
    public function testDeprecation()
    {
        $this->assertTrue(true, 'This avoids the risky status');
        @trigger_error('This "Foo" method is deprecated.', E_USER_DEPRECATED);
    }
}
