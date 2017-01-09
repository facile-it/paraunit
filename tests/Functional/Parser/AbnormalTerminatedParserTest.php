<?php

namespace Tests\Functional\Parser;

use Paraunit\Parser\JSON\AbnormalTerminatedParser;
use Paraunit\Parser\JSON\LogFetcher;
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
        $log->status = LogFetcher::LOG_ENDING_STATUS;
        $log->test = 'testFunction()';
        /** @var AbnormalTerminatedParser $parser */
        $parser = $this->container->get('paraunit.parser.abnormal_terminated_parser');

        $parsedResult = $parser->handleLogItem($process, $log);

        $this->assertInstanceOf('Paraunit\TestResult\TestResultWithAbnormalTermination', $parsedResult);
    }

    /**
     * @dataProvider otherStatusesProvider
     */
    public function testHandleLogItemWithUncatchedLog($otherStatuses)
    {
        $process = new StubbedParaunitProcess();
        $log = new \stdClass();
        $log->status = $otherStatuses;
        /** @var AbnormalTerminatedParser $parser */
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
