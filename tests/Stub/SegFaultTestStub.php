<?php

declare(strict_types=1);

namespace Tests\Stub;

/**
 * Class SegFaultTestStub
 */
class SegFaultTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest()
    {
        exit(139);
    }
}
