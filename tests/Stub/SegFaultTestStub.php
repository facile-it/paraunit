<?php

declare(strict_types=1);

namespace Tests\Stub;

class SegFaultTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest(): never
    {
        exit(139);
    }
}
