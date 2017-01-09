<?php

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\UnknownResultParser;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class UnknownResultParserTest
 * @package Tests\Unit\Parser\JSON
 */
class UnknownResultParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider statusesProvider
     */
    public function testHandleLogItemShouldCatchAnything($statuses)
    {
        $log = new \stdClass();
        $log->status = $statuses;
        $log->message = 'message';

        $factory = $this->prophesize('Paraunit\TestResult\TestResultFactory');
        $factory->createFromLog($log)
            ->shouldBeCalled()
            ->willReturn($this->mockPrintableTestResult());
        $resultContainer = $this->prophesize('Paraunit\TestResult\Interfaces\TestResultHandlerInterface');
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldBeCalled();

        $parser = new UnknownResultParser($factory->reveal(), $resultContainer->reveal(), 'no-status-required');
        $this->assertNotNull($parser->handleLogItem(new StubbedParaunitProcess(), $log));
    }

    public function statusesProvider()
    {
        return array(
            array('pass'),
            array('error'),
            array('fail'),
            array('pass'),
            array('testStart'),
            array('suiteStart'),
            array('qwerty'),
            array('trollingYou'),
        );
    }
}
