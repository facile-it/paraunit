<?php

namespace Paraunit\Tests\Stub;

/**
 * Interface BrokenTestInterface
 * @package Paraunit\Tests\Stub
 */
interface BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    public function testBrokenTest();
}
