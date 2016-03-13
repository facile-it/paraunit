<?php

namespace Tests\Stub;

/**
 * Interface BrokenTestInterface
 * @package Tests\Stub
 */
interface BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    public function testBrokenTest();
}
