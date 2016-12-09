<?php

namespace Tests\Unit\TestResult;

use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithAbnormalTermination;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class TestResultContainerTest
 * @package Tests\Unit\TestResult
 */
class TestResultContainerTest extends BaseUnitTestCase
{
    public function testHandleLogItemAddsProcessOutputWhenNeeded()
    {
        $testResult = new TestResultWithAbnormalTermination($this->mockTestFormat(), 'function name', 'fail message');
        $process = new StubbedParaunitProcess();
        $process->setOutput('test output');

        $testResultContainer = new TestResultContainer($this->mockTestFormat());
        $testResultContainer->handleTestResult($process, $testResult);

        $this->assertContains('fail message', $testResult->getFailureMessage());
        $this->assertContains('test output', $testResult->getFailureMessage());
    }

    public function testHandleLogItemAddsMessageWhenProcessOutputIsEmpty()
    {
        $testResult = new TestResultWithAbnormalTermination($this->mockTestFormat(), 'function name', 'fail message');
        $process = new StubbedParaunitProcess();
        $process->setOutput(null);

        $format = $this->prophesize('Paraunit\TestResult\TestResultFormat');
        $format->getTag()->willReturn('tag');

        $testResultContainer = new TestResultContainer($format->reveal());
        $testResultContainer->handleTestResult($process, $testResult);

        $this->assertContains('fail message', $testResult->getFailureMessage());
        $this->assertContains('<tag><[NO OUTPUT FOUND]></tag>', $testResult->getFailureMessage());
    }
}
