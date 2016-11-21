<?php

namespace Tests\Unit\Parser;

use Paraunit\Parser\JSONLogFetcher;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class JSONLogFetcherTest
 * @package Tests\Unit\Parser
 */
class JSONLogFetcherTest extends BaseUnitTestCase
{
    public function testFetchAppendsLogEndingAnywayWithMissingLog()
    {
        $process = new StubbedParaunitProcess();

        $tempFileNameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFileNameFactory->getFilenameForLog($process->getUniqueId())->willReturn('non-existent-log.json');

        $fetcher = new JSONLogFetcher($tempFileNameFactory->reveal());

        $logs = $fetcher->fetch($process);

        $this->assertNotNull($logs, 'Fetcher returning a non-array');
        $this->assertTrue(is_array($logs), 'Fetcher returning a non-array');
        $this->assertCount(1, $logs, 'Log ending missing');
        $this->assertContainsOnlyInstancesOf('\stdClass', $logs);

        $endingLog = end($logs);
        $this->assertTrue(property_exists($endingLog, 'status'));
        $this->assertEquals(JSONLogFetcher::LOG_ENDING_STATUS, $endingLog->status);
    }

    public function testFetch()
    {
        $process = new StubbedParaunitProcess();
        $filename = __DIR__ . '/../../Stub/PHPUnitJSONLogOutput/AllGreen.json';
        $this->assertTrue(file_exists($filename), 'Test malformed, stub log file not found');

        $tempFileNameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFileNameFactory->getFilenameForLog($process->getUniqueId())->willReturn($filename);

        $fetcher = new JSONLogFetcher($tempFileNameFactory->reveal());

        $logs = $fetcher->fetch($process);

        $this->assertNotNull($logs, 'Fetcher returning a non-array');
        $this->assertTrue(is_array($logs), 'Fetcher returning a non-array');
        $this->assertCount(20 + 1, $logs, 'Log ending missing');
        $this->assertContainsOnlyInstancesOf('\stdClass', $logs);

        $endingLog = end($logs);
        $this->assertTrue(property_exists($endingLog, 'status'));
        $this->assertEquals(JSONLogFetcher::LOG_ENDING_STATUS, $endingLog->status);
    }
}
