<?php
declare(strict_types=1);

namespace Tests\Unit\TestResult;

use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\TestResult\TestResultFactory;
use Paraunit\TestResult\TestResultFormat;
use Tests\BaseUnitTestCase;
use Paraunit\TestResult\MuteTestResult;
use Paraunit\TestResult\TestResultWithMessage;
use Paraunit\TestResult\FullTestResult;
use Paraunit\TestResult\TraceStep;
use Paraunit\TestResult\TestResultWithAbnormalTermination;

/**
 * Class TestResultFactoryTest
 * @package Tests\Unit\TestResult
 */
class TestResultFactoryTest extends BaseUnitTestCase
{
    public function testCreateFromLogMuteWithoutTestName()
    {
        $log = new \stdClass();
        $log->event = 'test';

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed'));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(MuteTestResult::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
    }

    public function testCreateFromLogMute()
    {
        $log = new \stdClass();
        $log->test = 'testFunction()';
        $log->event = 'test';

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed'));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(MuteTestResult::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
    }

    public function testCreateFromLogWithMessage()
    {
        $log = $this->getLogFromStub('test', 'error');
        unset($log->trace);

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed'));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(TestResultWithMessage::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
        $this->assertEquals($log->test, $result->getFunctionName());
    }

    public function testCreateFromLogWithTrace()
    {
        $log = $this->getLogWithTrace();
        $log->trace[] = clone $log->trace[0];

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed'));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(FullTestResult::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
        $this->assertEquals($log->message, $result->getFailureMessage());
        $trace = $result->getTrace();
        $this->assertCount(count($log->trace), $trace);
        $this->assertContainsOnlyInstancesOf(TraceStep::class, $trace);
        $i = 0;
        do {
            $this->assertEquals($log->trace[$i]->file, $trace[$i]->getFilePath());
            $this->assertEquals($log->trace[$i]->line, $trace[$i]->getLine());
        } while (++$i < count($log->trace));
    }

    public function testCreateFromLogWithAbnormalTermination()
    {
        $log = $this->getLogFromStub();
        $log->status = LogFetcher::LOG_ENDING_STATUS;
        $log->test = 'testFunction()';

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed'));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf(TestResultWithAbnormalTermination::class, $result);
        $this->assertInstanceOf(TestResultFormat::class, $result->getTestResultFormat());
        // TestStartParser injects the last launched test function name
        $this->assertEquals($log->test, $result->getFunctionName());
        $this->assertStringStartsWith('Abnormal termination -- complete test output:', $result->getFailureMessage());
    }
}
