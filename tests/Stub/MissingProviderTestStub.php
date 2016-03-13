<?php

namespace Tests\Stub;


class MissingProviderTestStub extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider missingProvider
     */
    public function testWithMissingProvider($values)
    {
        $this->assertNotNull($values);
    }
}
