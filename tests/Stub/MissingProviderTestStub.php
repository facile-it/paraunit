<?php

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

class MissingProviderTestStub extends TestCase
{
    /**
     * @dataProvider missingProvider
     */
    public function testWithMissingProvider($values)
    {
        $this->assertNotNull($values);
    }
}
