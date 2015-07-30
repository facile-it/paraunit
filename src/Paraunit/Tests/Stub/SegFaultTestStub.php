<?php

namespace Paraunit\Tests\Stub;

class SegFaultTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest()
    {
        // segfault
        preg_match("/http:\/\/(.)+\.ru/i", str_repeat('http://google.ru', 2000));

        // segfault for PHP7/HHVM
        preg_match("/http:\/\/(.)+\.ru/i", str_repeat('http://google.ruhttp://google.ruhttp://google.ruhttp://google.ruhttp://google.ru', 9999999999999));
    }
}
