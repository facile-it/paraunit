<?php

namespace Tests\Unit\Parser;


use Paraunit\Parser\UnknownResultParser;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

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
        $factory->createFromLog($log)->shouldBeCalled()->willReturn($this->mockPrintableTestResult());

        $parser = new UnknownResultParser($factory->reveal(), 'no-status-required');
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
