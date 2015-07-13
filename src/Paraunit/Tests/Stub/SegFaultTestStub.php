<?php

namespace Paraunit\Tests\Stub;


class SegFaultTestStub extends BrokenTestBase implements BrokenTestInterface
{
    function testBrokenTest()
    {
        // segfault
        preg_match("/http:\/\/(.)+\.ru/i", str_repeat("http://google.ru", 2000));
    }
}