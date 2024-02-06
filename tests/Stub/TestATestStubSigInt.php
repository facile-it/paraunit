<?php

declare(strict_types=1);

namespace Tests\Stub;

class TestATestStubSigInt extends BrokenTestBase implements BrokenTestInterface
{
    public function testBrokenTest(): void
    {
        $chunkFileName = __DIR__ . DIRECTORY_SEPARATOR . 'phpunit_for_sigint_stubs_0.xml';
        $otherChunkFileName = __DIR__ . DIRECTORY_SEPARATOR . 'phpunit_for_sigint_stubs_1.xml';

        // wait 5s for other chunk to finish
        $this->waitIfFileExist($otherChunkFileName, 5);

        $this->assertFileExists($chunkFileName);

        posix_kill(posix_getppid(), SIGINT);

        // give parent process 5s to react
        $this->waitIfFileExist($chunkFileName, 5);

        $this->assertFileDoesNotExist($chunkFileName);
    }

    private function waitIfFileExist(
        string $fileName,
        int $time
    ): void {
        for ($i = 1; $i <= $time * 20; ++$i) {
            usleep(50_000);
            if (! file_exists($fileName)) {
                break;
            }
        }
    }
}
