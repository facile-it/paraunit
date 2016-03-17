<?php

namespace Tests\Stub;

/**
 * Class ParseErrorTestStub
 * @package Tests\Stub
 */
class ParseErrorTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public static function setUpBeforeClass()
    {
//        This would create a situation where the log is missing!
        I want to create a parse error!
    }

    public function testBrokenTest()
    {
        $this->assertTrue(true);
    }
}
