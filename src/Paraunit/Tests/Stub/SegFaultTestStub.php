<?php

namespace Paraunit\Tests\Stub;

/**
 * Class SegFaultTestStub
 * @package Paraunit\Tests\Stub
 */
class SegFaultTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest()
    {
        if (extension_loaded('sigsegv')) {
            sigsegv();
        }else{
            preg_match("/http:\/\/(.)+\.ru/i", str_repeat('http://google.ru', 2000));
        }
    }
}
