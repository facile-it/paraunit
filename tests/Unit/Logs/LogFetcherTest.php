<?php

declare(strict_types=1);

namespace Tests\Unit\Logs;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Logs\JSON\LogFetcher;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class LogFetcherTest extends BaseUnitTestCase
{
    public function testFetchReturnsEmptyListWithMissingLog(): void
    {
        $process = new StubbedParaunitProcess();

        $tempFileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFileNameFactory->getFilenameForLog($process->getUniqueId())
            ->willReturn('non-existent-log.json');

        $fetcher = new LogFetcher($tempFileNameFactory->reveal());

        $logs = $fetcher->fetch($process);

        $this->assertNotNull($logs, 'Fetcher returning a non-array');
        $this->assertEmpty($logs);
    }

    public function testFetch(): void
    {
        $process = new StubbedParaunitProcess();
        $filename = $this->createStubLog();

        $tempFileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFileNameFactory->getFilenameForLog($process->getUniqueId())
            ->willReturn($filename);

        $fetcher = new LogFetcher($tempFileNameFactory->reveal());

        $logs = $fetcher->fetch($process);

        $this->assertNotNull($logs, 'Fetcher returning a non-array');
        $this->assertCount(4 + 1, $logs, 'Log ending missing');
        $this->assertContainsOnlyInstancesOf(LogData::class, $logs);

        $endingLog = end($logs);
        $this->assertInstanceOf(LogData::class, $endingLog);
        $this->assertEquals(LogStatus::LogTerminated, $endingLog->status);

        $this->assertFileDoesNotExist($filename, 'Log file should be deleted to preserve memory');
    }

    private function createStubLog(): string
    {
        $logs = $this->createLogsForOnePassedTest();

        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testfile.json';

        foreach ($logs as $log) {
            file_put_contents($filename, json_encode($log, JSON_THROW_ON_ERROR), FILE_APPEND);
        }

        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        return $filename;
    }
}
