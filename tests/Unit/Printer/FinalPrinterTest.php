<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Printer\FinalPrinter;
use Paraunit\TestResult\TestOutcomeContainer;
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
        ClockMock::register(self::class);
        $output = new UnformattedOutputStub();
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->shouldBeCalled()
            ->willReturn(false);

        $testResultContainer = $this->prophesize(TestOutcomeContainer::class);
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

        $printer = new FinalPrinter($testResultList->reveal(), $output, $chunkSize->reveal());

        ClockMock::withClockMock(true);

        $printer->onEngineStart();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessToBeRetried();
        $printer->onProcessTerminated();
        usleep(60_499_999);
        $printer->onEngineEnd();

        ClockMock::withClockMock(false);

        $this->assertStringContainsString('Execution time -- 00:01:00', $output->getOutput());
        $this->assertStringContainsString('Executed: 5 test classes (1 retried), 44 tests', $output->getOutput());
    }

    public function testOnEngineEndPrintsTheRightChunkedCountSummary(): void
    {
        $output = new UnformattedOutputStub();
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->shouldBeCalled()
            ->willReturn(true);

        $testResultContainer = $this->prophesize(TestOutcomeContainer::class);
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

        $printer = new FinalPrinter($testResultList->reveal(), $output, $chunkSize->reveal());

        $printer->onEngineStart();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessTerminated();
        $printer->onProcessToBeRetried();
        $printer->onProcessTerminated();
        $printer->onEngineEnd();

        $this->assertStringContainsString('Executed: 5 chunks (1 retried), 44 tests', $output->getOutput());
    }

    public function testOnEngineEndHandlesEmptyMessagesCorrectly(): void
    {
        $testResultContainer = $this->prophesize(TestOutcomeContainer::class);
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
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->shouldBeCalled()
            ->willReturn(false);

        $printer = new FinalPrinter($testResultList->reveal(), $output, $chunkSize->reveal());

        $printer->onEngineStart();
        $printer->onEngineEnd();

        $this->assertStringNotContainsString('output', $output->getOutput());
    }
}
