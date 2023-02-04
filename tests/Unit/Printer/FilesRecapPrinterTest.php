<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Printer\FilesRecapPrinter;
use Paraunit\TestResult\TestOutcomeContainer;
use Paraunit\TestResult\TestResultFormat;
use Paraunit\TestResult\TestResultList;
use Tests\BaseUnitTestCase;
use Tests\Stub\UnformattedOutputStub;

class FilesRecapPrinterTest extends BaseUnitTestCase
{
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
            ->willReturn([$this->mockTestResult()]);

        $testResultFormat = $this->prophesize(TestResultFormat::class);
        $testResultFormat->getTag()
            ->willReturn('tag');
        $testResultFormat->getTitle()
            ->willReturn('title');
        $testResultFormat->shouldPrintFilesRecap()
            ->shouldBeCalled()
            ->willReturn(true);

        $testResultContainer->getTestResultFormat()
            ->willReturn($testResultFormat->reveal());
        $testResultContainer->getFileNames()
            ->willReturn(['Test.php']);

        $testResultList = $this->prophesize(TestResultList::class);
        $testResultList->getTestResultContainers()
            ->willReturn([$testResultContainer->reveal()]);

        $printer = new FilesRecapPrinter($testResultList->reveal(), $output, $chunkSize->reveal());

        $printer->onEngineEnd();

        $this->assertStringContainsString('1 chunks with TITLE:', $output->getOutput());
    }
}
