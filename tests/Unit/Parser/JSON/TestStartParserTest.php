<?php
declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\Parser\JSON\TestStartParser;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class TestStartParserTest
 * @package Tests\Unit\Parser\JSON
 */
class TestStartParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider logsProvider
     */
    public function testHandleLogItem(string $event, bool $chainInterrupted, bool $processExpectsTestResult = false)
    {
        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(true);
        $parser = new TestStartParser();
        $log = new \stdClass();
        $log->event = $event;
        $log->test = 'testFunction';

        $return = $parser->handleLogItem($process, $log);

        if ($chainInterrupted) {
            $this->assertInstanceOf(TestResultInterface::class, $return);
        } else {
            $this->assertNull($return);
        }

        if ($processExpectsTestResult) {
            $this->assertTrue($process->isWaitingForTestResult());
        }
    }

    public function logsProvider(): array
    {
        return [
            ['testStart', true, true],
            ['suiteStart', true, true],
            ['test', false, false],
            ['aaaa', false, false],
        ];
    }

    public function testHandleLogItemCatchesEndingIfGraceful()
    {
        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(false);
        $parser = new TestStartParser();
        $log = new \stdClass();
        $log->status = LogFetcher::LOG_ENDING_STATUS;

        $return = $parser->handleLogItem($process, $log);

        $this->assertInstanceOf(TestResultInterface::class, $return);
    }

    public function testHandleLogItemAppendsNoCulpableFunctionForMissingLog()
    {
        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(true);
        $parser = new TestStartParser();
        $log = new \stdClass();
        $log->status = LogFetcher::LOG_ENDING_STATUS;

        $return = $parser->handleLogItem($process, $log);

        $this->assertNull($return);
        $this->assertEquals('UNKNOWN -- log not found', $log->test);
    }

    public function testHandleLogItemAppendsCulpableFunction()
    {
        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(true);
        $parser = new TestStartParser();
        $log = new \stdClass();
        $log->event = 'testStart';
        $log->test = 'testFunction';

        $parser->handleLogItem($process, $log);

        $log->status = LogFetcher::LOG_ENDING_STATUS;

        $return = $parser->handleLogItem($process, $log);

        $this->assertNull($return, 'Parsing should not be interrupted');
        $this->assertEquals('testFunction', $log->test);
    }

    public function testHandleLogItemAppendsCulpableFunctionToRightProcess()
    {
        $parser = new TestStartParser();
        $log = new \stdClass();
        $log->event = 'testStart';
        $log->test = 'testFunction';

        $parser->handleLogItem(new StubbedParaunitProcess(), $log);

        $log = new \stdClass();
        $log->status = LogFetcher::LOG_ENDING_STATUS;
        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(true);

        $return = $parser->handleLogItem($process, $log);

        $this->assertNull($return, 'Parsing should not be interrupted');
        $this->assertEquals('UNKNOWN -- log not found', $log->test);
    }
}
