<?php

namespace Tests\Unit\Parser;


use Paraunit\Parser\JSONLogFetcher;
use Paraunit\Parser\TestStartParser;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class TestStartParserTest
 * @package Unit\Parser
 */
class TestStartParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider logsProvider
     */
    public function testHandleLogItem($status, $chainInterrupted, $processExpectsTestResult = false)
    {
        $process = new StubbedParaunitProcess();
        $parser = new TestStartParser();
        $log = new \stdClass();
        $log->status = $status;
        $log->test = 'testFunction';

        $return = $parser->handleLogItem($process, $log);

        if ($chainInterrupted) {
            $this->assertInstanceOf('Paraunit\TestResult\TestResultInterface', $return);
        } else {
            $this->assertNull($return);
        }

        if ($processExpectsTestResult) {
            $this->assertTrue($process->isWaitingForTestResult());
        }
    }

    public function logsProvider()
    {
        return array(
            array('testStart', true, true),
            array('suiteStart', true, true),
            array('test', false),
            array('aaaa', false),
            array(JSONLogFetcher::LOG_ENDING_STATUS, false, false),
        );
    }
}
