<?php

namespace Tests\Unit\TestResult;


use Paraunit\TestResult\TestResultFactory;
use Paraunit\TestResult\TestResultFormat;
use Tests\BaseUnitTestCase;

/**
 * Class TestResultFactoryTest
 * @package Tests\Unit\TestResult
 */
class TestResultFactoryTest extends BaseUnitTestCase
{
    public function testCreateFromLogNull()
    {
        $log = new \stdClass();

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed', ''));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf('Paraunit\TestResult\NullTestResult', $result);
        $this->assertInstanceOf('Paraunit\TestResult\TestResultFormat', $result->getTestResultFormat());
    }

    public function testCreateFromLogMuteWithoutTestName()
    {
        $log = new \stdClass();
        $log->status = 'status';

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed', ''));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf('Paraunit\TestResult\MuteTestResult', $result);
        $this->assertInstanceOf('Paraunit\TestResult\TestResultFormat', $result->getTestResultFormat());
    }

    public function testCreateFromLogMute()
    {
        $log = new \stdClass();
        $log->test = 'test';
        $log->status = 'status';

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed', ''));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf('Paraunit\TestResult\MuteTestResult', $result);
        $this->assertInstanceOf('Paraunit\TestResult\TestResultFormat', $result->getTestResultFormat());
    }

    public function testCreateFromLogWithMessage()
    {
        $log = $this->getLogWithStatus('error');
        unset($log->trace);

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed', ''));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf('Paraunit\TestResult\TestResultWithMessage', $result);
        $this->assertInstanceOf('Paraunit\TestResult\TestResultFormat', $result->getTestResultFormat());
        $this->assertEquals($log->message, $result->getFailureMessage());
    }

    public function testCreateFromLogWithTrace()
    {
        $log = $this->getLogWithStatus('error');
        $log->trace[] = clone $log->trace[0];

        $factory = new TestResultFactory(new TestResultFormat('?', 'concealed', ''));
        $result = $factory->createFromLog($log);

        $this->assertInstanceOf('Paraunit\TestResult\FullTestResult', $result);
        $this->assertInstanceOf('Paraunit\TestResult\TestResultFormat', $result->getTestResultFormat());
        $this->assertEquals($log->message, $result->getFailureMessage());
        $trace = $result->getTrace();
        $this->assertEquals(count($log->trace), count($trace));
        $this->assertContainsOnlyInstancesOf('Paraunit\TestResult\TraceStep', $trace);
        $i = 0;
        do {
            $this->assertEquals($log->trace[$i]->file, $trace[$i]->getFilePath());
            $this->assertEquals($log->trace[$i]->line, $trace[$i]->getLine());
        } while(++$i < count($log->trace));
    }
}
