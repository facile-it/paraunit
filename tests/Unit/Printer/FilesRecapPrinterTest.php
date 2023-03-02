<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Logs\ValueObject\TestMethod;
use Paraunit\Printer\FilesRecapPrinter;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;
use Prophecy\Argument;
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

        $testResultContainer = $this->prophesize(TestResultContainer::class);
        $testResultContainer->getTestResults(Argument::cetera())
            ->willReturn([]);
        $testResultContainer->getTestResults(TestOutcome::Failure)
            ->willReturn([
                new TestResult(new TestMethod('FooTest', 'test1'), TestOutcome::Failure),
                new TestResult(new TestMethod('FooTest', 'test2'), TestOutcome::Failure),
                new TestResult(new TestMethod('FooTest', 'test3'), TestOutcome::Failure),
            ]);

        $testResultContainer->getFileNames(Argument::cetera())
            ->willReturn([]);
        $testResultContainer->getFileNames(TestOutcome::Failure)
            ->willReturn(['FooTest.php']);

        $printer = new FilesRecapPrinter($output, $testResultContainer->reveal(), $chunkSize->reveal());

        $printer->onEngineEnd();

        $this->assertStringContainsString('1 chunks with FAILURES:', $output->getOutput());
    }
}
