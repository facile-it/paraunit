<?php

declare(strict_types=1);

namespace Tests\Stub;

class TestBSigIntTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest(): void
    {
        usleep(1000000);
    }
}
