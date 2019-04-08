<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Printer\FinalPrinter;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultList;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Stopwatch\Stopwatch;
use Tests\BaseUnitTestCase;
use Tests\Stub\UnformattedOutputStub;

class FinalPrinterTest extends BaseUnitTestCase
{
    public function testOnEngineEndPrintsTheRightCountSummary(): void
    {
        ClockMock::register(Stopwatch::class);
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
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessToBeRetried();
        $printer->onProcessTerminated();
        usleep(60499999);
        $printer->onEngineEnd();

        ClockMock::withClockMock(false);

        $this->assertContains('Execution time -- 00:01:00', $output->getOutput());
        $this->assertContains('Executed: 5 test classes (1 retried), 44 tests', $output->getOutput());
    }

    public function testOnEngineEndHandlesEmptyMessagesCorrectly(): void
    {
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
        $output = new UnformattedOutputStub();

        $printer = new FinalPrinter($testResultList->reveal(), $output);

        $printer->onEngineStart();
        $printer->onEngineEnd();

        $this->assertNotContains('output', $output->getOutput());
    }
}
