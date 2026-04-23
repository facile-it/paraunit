<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Printer\FailuresPrinter;
use Paraunit\Printer\PrinterConfiguration;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Paraunit\TestResult\ValueObject\TestIssue;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\UnformattedOutputStub;

class FailuresPrinterTest extends BaseUnitTestCase
{
    public function testOnEngineEndSkipsTestIssueOutputWhenPrinterConfigurationDisablesIt(): void
    {
        $output = new UnformattedOutputStub();
        $printerConfiguration = $this->prophesize(PrinterConfiguration::class);
        $printerConfiguration->shouldPrint(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn(false);

        $printer = new FailuresPrinter(
            $output,
            $this->mockTestResultContainer(),
            $printerConfiguration->reveal(),
        );

        $printer->onEngineEnd();

        $this->assertStringNotContainsStringIgnoringCase('Warnings output:', $output->getOutput());
    }

    public function testOnEngineEndPrintsTestIssueOutputWhenPrinterConfigurationEnablesIt(): void
    {
        $output = new UnformattedOutputStub();
        $printerConfiguration = $this->prophesize(PrinterConfiguration::class);
        $printerConfiguration->shouldPrint(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn(true);

        $printer = new FailuresPrinter(
            $output,
            $this->mockTestResultContainer(),
            $printerConfiguration->reveal(),
        );

        $printer->onEngineEnd();

        $this->assertStringContainsString('Warnings output:', $output->getOutput());
        $this->assertStringContainsString('FooTest::test_bar', $output->getOutput());
        $this->assertStringContainsString('Something went wrong', $output->getOutput());
    }

    private function mockTestResultContainer(): TestResultContainer
    {
        $testResultContainer = $this->prophesize(TestResultContainer::class);
        $testResultContainer->getTestResults(Argument::cetera())
            ->willReturn([]);
        $testResultContainer->getTestResults(TestIssue::Warning)
            ->willReturn([
                new TestResultWithMessage(new Test('FooTest::test_bar'), TestIssue::Warning, 'Something went wrong'),
            ]);

        return $testResultContainer->reveal();
    }
}
