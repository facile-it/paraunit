<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\LogFetcher;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class LogFetcherTest
 * @package Tests\Unit\Parser\JSON
 */
class LogFetcherTest extends BaseUnitTestCase
{
    public function testFetchAppendsLogEndingAnywayWithMissingLog()
    {
        $process = new StubbedParaunitProcess();

        $tempFileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFileNameFactory->getFilenameForLog($process->getUniqueId())
            ->willReturn('non-existent-log.json');

        $fetcher = new LogFetcher($tempFileNameFactory->reveal());

        $logs = $fetcher->fetch($process);

        $this->assertNotNull($logs, 'Fetcher returning a non-array');
        $this->assertInternalType('array', $logs, 'Fetcher returning a non-array');
        $this->assertCount(1, $logs, 'Log ending missing');
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $logs);

        $endingLog = end($logs);
        $this->assertTrue(property_exists($endingLog, 'status'));
        $this->assertEquals(LogFetcher::LOG_ENDING_STATUS, $endingLog->status);
    }

    public function testFetch()
    {
        $process = new StubbedParaunitProcess();
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testfile.json';
        copy(__DIR__ . '/../../../Stub/PHPUnitJSONLogOutput/AllGreen.json', $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFileNameFactory->getFilenameForLog($process->getUniqueId())
            ->willReturn($filename);

        $fetcher = new LogFetcher($tempFileNameFactory->reveal());

        $logs = $fetcher->fetch($process);

        $this->assertNotNull($logs, 'Fetcher returning a non-array');
        $this->assertInternalType('array', $logs, 'Fetcher returning a non-array');
        $this->assertCount(20 + 1, $logs, 'Log ending missing');
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $logs);

        $endingLog = end($logs);
        $this->assertTrue(property_exists($endingLog, 'status'));
        $this->assertEquals(LogFetcher::LOG_ENDING_STATUS, $endingLog->status);

        $this->assertFileNotExists($filename, 'Log file should be deleted to preserve memory');
    }
}
