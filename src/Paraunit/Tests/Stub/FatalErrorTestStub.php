<?php

namespace Paraunit\Tests\Stub;

class FatalErrorTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest()
    {
        ini_set('memory_limit', '2M');

        $arr = array();

        while(true) {
            $arr[] = "Allocated memory... allocated memory everywhere!";
        }
    }
}
