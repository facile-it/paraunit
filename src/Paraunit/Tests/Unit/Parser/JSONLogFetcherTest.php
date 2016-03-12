<?php

namespace Paraunit\Tests\Unit\Parser;

use Paraunit\Parser\JSONLogFetcher;
use Paraunit\Tests\BaseUnitTestCase;
use Paraunit\Tests\Stub\StubbedParaProcess;

/**
 * Class JSONLogFetcherTest
 * @package Paraunit\Tests\Unit\Parser
 */
class JSONLogFetcherTest extends BaseUnitTestCase
{
    public function testFetchThrowsExceptionWithMissingLog()
    {
        $process = new StubbedParaProcess();

        $fileName = $this->prophesize('Paraunit\Configuration\JSONLogFilename');
        $fileName->generate($process)->willReturn('log.json');

        $fetcher = new JSONLogFetcher($fileName->reveal());

        $this->setExpectedException('Paraunit\Exception\JSONLogNotFoundException');

        $fetcher->fetch($process);
    }

    public function testFetch()
    {
        $process = new StubbedParaProcess();

        $fileName = $this->prophesize('Paraunit\Configuration\JSONLogFilename');
        $fileName->generate($process)->willReturn(__DIR__ . '/../../Stub/PHPUnitOutput/JSONLogs/AllGreen.json');

        $fetcher = new JSONLogFetcher($fileName->reveal());

        $logs = $fetcher->fetch($process);

        $this->assertTrue(is_array($logs), 'Fetcher returning a non-array');
        $this->assertCount(20, $logs);
        $this->assertContainsOnlyInstancesOf('\stdClass', $logs);
    }
}
