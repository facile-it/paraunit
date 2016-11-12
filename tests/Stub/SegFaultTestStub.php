<?php

namespace Tests\Stub;

/**
 * Class SegFaultTestStub
 * @package Tests\Stub
 */
class SegFaultTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest()
    {
        exit(139);
    }
}
