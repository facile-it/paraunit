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
}
