<?php

declare(strict_types=1);

namespace Tests\Stub;

/**
 * Interface BrokenTestInterface
 */
interface BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    public function testBrokenTest();
}
