<?php

namespace Tests\Functional\Parser;

use Paraunit\Parser\JSONLogFetcher;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class AbnormalTerminatedParserTest
 * @package Functional\Parser
 */
class AbnormalTerminatedParserTest extends BaseFunctionalTestCase
{
    public function testHandleLogItemWithAbnormalTermination()
    {
        $process = new StubbedParaunitProcess();
        $log = new \stdClass();
        $log->status = JSONLogFetcher::LOG_ENDING_STATUS;
        $log->message = 'message';
        $parser = $this->container->get('paraunit.parser.abnormal_terminated_parser');

        $parsedResult = $parser->handleLogItem($process, $log);

        $this->assertInstanceOf('Paraunit\TestResult\TestResultInterface', $parsedResult);
        $this->assertTrue($process->hasAbnormalTermination());
    }

    /**
     * @dataProvider otherStatusesProvider
     */
    public function testHandleLogItemWithUncatchedLog($otherStatuses)
    {
        $process = new StubbedParaunitProcess();
        $log = new \stdClass();
        $log->status = $otherStatuses;
        $log->message = 'message';
        $parser = $this->container->get('paraunit.parser.abnormal_terminated_parser');

        $parsedResult = $parser->handleLogItem($process, $log);

        $this->assertNull($parsedResult);
        $this->assertFalse($process->hasAbnormalTermination());
    }

    public function otherStatusesProvider()
    {
        return array(
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
