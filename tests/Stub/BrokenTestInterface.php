<?php

declare(strict_types=1);

namespace Tests\Stub;

interface BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    public function testBrokenTest();
}
