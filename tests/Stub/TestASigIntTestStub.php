<?php

declare(strict_types=1);

namespace Tests\Stub;

class TestASigIntTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest(): void
    {
        usleep(500000);
        posix_kill(posix_getppid(), SIGINT);
    }
}
