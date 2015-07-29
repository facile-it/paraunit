<?php

namespace Paraunit\Tests\Stub;

interface BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    public function testBrokenTest();
}
