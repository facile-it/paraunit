<?php

namespace Tests\Unit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Printer\FinalPrinter;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class FailuresPrinterTest
 * @package Tests\Unit\Printer
 */
class FinalPrinterTest extends BaseUnitTestCase
{
    public function testOnEngineEndPrintsTheRightCountSummary()
    {
        $output = new UnformattedOutputStub();
        $context = array(
            'start' => new \DateTime('-1 minute'),
            'end' => new \DateTime(),
            'process_completed' => array_fill(0, 15, new StubbedParaunitProcess()),
        );
        $engineEvent = new EngineEvent($output, $context);

        $testResultContainer = $this->prophesize('Paraunit\TestResult\TestResultContainer');
        $testResultContainer->countTestResults()->willReturn(3);
        $testResultContainer->getTestResults()->willReturn(array_fill(0, 3, $this->mockTestResult()));
        $testResultContainer->getTestResultFormat()->willReturn($this->mockTestFormat());
        $testResultContainer->getFileNames()->willReturn(array('Test.php'));

        $testResultList = $this->prophesize('Paraunit\TestResult\TestResultList');
        $testResultList->getTestResultContainers()->willReturn(array_fill(0, 15, $testResultContainer->reveal()));

        $printer = new FinalPrinter($testResultList->reveal());

        $printer->onEngineEnd($engineEvent);

        $this->assertContains('Execution time -- 00:01:00', $output->getOutput());
        $this->assertContains('Executed: 15 test classes, 45 tests', $output->getOutput());
    }

    public function testOnEngineEndHandlesEmptyMessagesCorrectly()
    {
        $output = new UnformattedOutputStub();
        $context = array(
            'start' => new \DateTime('-1 minute'),
            'end' => new \DateTime(),
            'process_completed' => array(new StubbedParaunitProcess()),
        );
        $engineEvent = new EngineEvent($output, $context);

        $testResultContainer = $this->prophesize('Paraunit\TestResult\TestResultContainer');
        $testResultContainer->countTestResults()->willReturn(3);
        $testResultContainer->getTestResults()->willReturn(array_fill(0, 3, $this->mockTestResult()));
        $testResultContainer->getTestResultFormat()->willReturn($this->mockTestFormat());
        $testResultContainer->getFileNames()->willReturn(array('Test.php'));

        $testResultList = $this->prophesize('Paraunit\TestResult\TestResultList');
        $testResultList->getTestResultContainers()->willReturn(array_fill(0, 15, $testResultContainer->reveal()));
        $printer = new FinalPrinter($testResultList->reveal());

        $printer->onEngineEnd($engineEvent);

        $this->assertNotContains('output', $output->getOutput());
    }
}
