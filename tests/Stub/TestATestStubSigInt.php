<?php

declare(strict_types=1);

namespace Tests\Stub;

class TestATestStubSigInt extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest(): void
    {
        usleep(100000);

        $chunkFileName = __DIR__ . DIRECTORY_SEPARATOR . 'phpunit_for_sigint_stubs_0.xml';

        $this->assertFileExists($chunkFileName);
        posix_kill(posix_getppid(), SIGINT);
        $this->assertFileDoesNotExist($chunkFileName);
    }
}
