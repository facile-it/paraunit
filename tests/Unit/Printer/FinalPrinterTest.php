<?php
declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Printer\FinalPrinter;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultList;
use Symfony\Bridge\PhpUnit\ClockMock;
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
        ClockMock::register('Symfony\Component\Stopwatch\Stopwatch');
        ClockMock::register(__CLASS__);
        $output = new UnformattedOutputStub();

        $testResultContainer = $this->prophesize(TestResultContainer::class);
        $testResultContainer->countTestResults()
            ->willReturn(3);
        $testResultContainer->getTestResults()
            ->willReturn(array_fill(0, 3, $this->mockTestResult()));
        $testResultContainer->getTestResultFormat()
            ->willReturn($this->mockTestFormat());
        $testResultContainer->getFileNames()
            ->willReturn(['Test.php']);

        $testResultList = $this->prophesize(TestResultList::class);
        $testResultList->getTestResultContainers()
            ->willReturn(array_fill(0, 15, $testResultContainer->reveal()));

        $printer = new FinalPrinter($testResultList->reveal(), $output);

        ClockMock::withClockMock(true);

        $printer->onEngineStart();
        sleep(60);
        $printer->onEngineEnd();

        ClockMock::withClockMock(false);

        $this->assertContains('Execution time -- 00:01:00', $output->getOutput());
        $this->assertContains('Executed: 15 test classes, 45 tests', $output->getOutput());
    }

    public function testOnEngineEndHandlesEmptyMessagesCorrectly()
    {
        $output = new UnformattedOutputStub();
        $context = [
            'start' => new \DateTime('-1 minute'),
            'end' => new \DateTime(),
            'process_completed' => [new StubbedParaunitProcess()],
        ];
        $engineEvent = new EngineEvent($output, $context);

        $testResultContainer = $this->prophesize(TestResultContainer::class);
        $testResultContainer->countTestResults()
            ->willReturn(3);
        $testResultContainer->getTestResults()
            ->willReturn(array_fill(0, 3, $this->mockTestResult()));
        $testResultContainer->getTestResultFormat()
            ->willReturn($this->mockTestFormat());
        $testResultContainer->getFileNames()
            ->willReturn(['Test.php']);

        $testResultList = $this->prophesize(TestResultList::class);
        $testResultList->getTestResultContainers()
            ->willReturn(array_fill(0, 15, $testResultContainer->reveal()));
        $printer = new FinalPrinter($testResultList->reveal(), $output);

        $printer->onEngineStart();
        $printer->onEngineEnd();

        $this->assertNotContains('output', $output->getOutput());
    }
}
