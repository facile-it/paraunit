<?php

declare(strict_types=1);

namespace Tests\Functional\Runner;

use Paraunit\Bin\Paraunit;
use Paraunit\Runner\ChunkFile;
use Paraunit\Runner\Runner;
use Tests\BaseIntegrationTestCase;

use function function_exists;

class ChunkFileTest extends BaseIntegrationTestCase
{
    // TODO: test chunk output with normal outcomes -- test name is now available!
    // TODO: test chunk output with abnormal termination

    public function testChunkedAllStubsSuite(): void
    {
        $chunkCount = 8;

        $this->setOption('chunk-size', '2');
        $this->loadContainer();

        $output = $this->getConsoleOutput();

        $this->assertEquals(10, $this->executeRunner(), $output->getOutput());

        $outputText = $output->getOutput();
        $this->assertStringNotContainsString('Coverage', $outputText);

        $this->assertOutputOrder($output, [
            'PARAUNIT',
            Paraunit::getVersion(),
            '...',
            //            '     39',
            'Execution time',
            //            "Executed: $chunkCount chunks (12 retried), 24 tests",
            'Abnormal Terminations (fatal Errors, Segfaults) output:',
            'Errors output:',
            'Failures output:',
            'Warnings output:',
            'Deprecations output:',
            '3 chunks with ABNORMAL TERMINATIONS (FATAL ERRORS, SEGFAULTS):',
            '8 chunks with ERRORS:',
            '1 chunks with FAILURES:',
            '2 chunks with WARNINGS:',
            '1 chunks with DEPRECATIONS:',
            '5 chunks with RETRIED:',
        ]);

        $this->assertStringContainsString('Tests\Stub\EntityManagerClosedTestStub::testBrokenTest', $outputText);
        $this->assertStringContainsString('Blah Blah The EntityManager is closed Blah Blah', $outputText);

        $this->assertStringContainsString('Tests\Stub\MySQLDeadLockTestStub::testBrokenTest', $outputText);
        $this->assertStringContainsString('SQLSTATE[HY000]: General error: Deadlock found; try restarting transaction', $outputText);

        $this->assertStringContainsString('Tests\Stub\MySQLLockTimeoutTestStub::testBrokenTest', $outputText);
        $this->assertStringContainsString('SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction', $outputText);

        $this->assertStringContainsString('Tests\Stub\PostgreSQLDeadLockTestStub::testBrokenTest', $outputText);
        $this->assertStringContainsString('SQLSTATE[40P01]: Deadlock detected: 7 ERROR:  deadlock detected', $outputText);

        $this->assertStringContainsString("Tests\Stub\RaisingNoticeTestStub::testRaise with data set #0", $outputText);

        $this->assertStringContainsString("Tests\Stub\RaisingNoticeTestStub::testRaise with data set #1", $outputText);

        $this->assertStringContainsString("Tests\Stub\RaisingNoticeTestStub::testRaise with data set #2", $outputText);

        $this->assertStringContainsString('Tests\Stub\SQLiteDeadLockTestStub::testBrokenTest', $outputText);

        $this->assertStringContainsString('Tests\Stub\RaisingNoticeTestStub::testVarDump', $outputText);

        if ('disabled' !== getenv('SYMFONY_DEPRECATIONS_HELPER')) {
            $this->assertStringContainsString('There was 1 error:', $outputText);
            $this->assertStringContainsString('Tests\Stub\PostgreSQLDeadLockTestStub::testBrokenTest', $outputText);
            $this->assertStringContainsString('Exception: SQLSTATE[40P01]: Deadlock detected: 7 ERROR:  deadlock detected', $outputText);

            $this->assertStringContainsString('Remaining self deprecation notices (3)', $outputText);
            $this->assertStringContainsString('3x: This "Foo" method is deprecated', $outputText);
            $this->assertStringContainsString('3x in RaisingDeprecationTestStub::testDeprecation from Tests\Stub', $outputText);
        }

        /** @var ChunkFile $chunkFileService */
        $chunkFileService = $this->getService(ChunkFile::class);
        $fileFullPath = $this->getConfigForStubs();
        $this->assertFileExists($fileFullPath);
        foreach (range(0, $chunkCount - 1) as $chunkNumber) {
            $chunkFileName = $chunkFileService->getChunkFileName($fileFullPath, $chunkNumber);
            $this->assertFileDoesNotExist($chunkFileName);
        }
    }

    public function testChunkedSigIntHandling(): void
    {
        if (! function_exists('posix_kill')) {
            $this->markTestSkipped('posix_kill is unavailable');
        }

        $chunkCount = 2;

        $this->setOption('configuration', $this->getStubPath() . DIRECTORY_SEPARATOR . 'phpunit_for_sigint_stubs.xml');
        $this->setTextFilter('TestStubSigInt.php');
        $this->setOption('chunk-size', '2');
        $this->loadContainer();

        $output = $this->getConsoleOutput();

        $this->assertEquals(0, $this->executeRunner(), $output->getOutput());

        $outputText = $output->getOutput();
        $this->assertStringNotContainsString('Coverage', $outputText);
        $this->assertOutputOrder($output, [
            'PARAUNIT',
            Paraunit::getVersion(),
            '     3',
            'Execution time',
            "Executed: {$chunkCount} chunks, 3 tests",
            'Risky Outcome output:',
            '2 chunks with RISKY OUTCOME:',
        ]);

        $this->assertStringContainsString('Tests\Stub\TestBTestStubSigInt::testBrokenTest', $outputText);
        $this->assertStringContainsString('Tests\Stub\TestCTestStubSigInt::testBrokenTest', $outputText);
        $this->assertStringContainsString('This test did not perform any assertions', $outputText);

        /** @var ChunkFile $chunkFileService */
        $chunkFileService = $this->getService(ChunkFile::class);
        $fileFullPath = $this->getConfigForStubs();
        $this->assertFileExists($fileFullPath);
        foreach (range(0, $chunkCount - 1) as $chunkNumber) {
            $chunkFileName = $chunkFileService->getChunkFileName($fileFullPath, $chunkNumber);
            $this->assertFileDoesNotExist($chunkFileName);
        }
    }

    private function executeRunner(): int
    {
        /** @var Runner $runner */
        $runner = $this->getService(Runner::class);

        return $runner->run();
    }
}
