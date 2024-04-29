<?php

declare(strict_types=1);

namespace Tests\Functional\Printer;

use Paraunit\Printer\FilesRecapPrinter;
use Paraunit\Runner\Runner;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\PassThenRetryTestStub;

class FilesRecapPrinterTest extends BaseFunctionalTestCase
{
    public function testOnEngineEndPrintsInTheRightOrder(): void
    {
        $this->populateTestResultContainerWithAllPossibleStatuses();

        $printer = $this->getService(FilesRecapPrinter::class);
        $this->assertInstanceOf(FilesRecapPrinter::class, $printer);

        $printer->onEngineEnd();

        $output = $this->getConsoleOutput();

        $this->assertNotEmpty($output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('PASSED output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('SKIPPED output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('INCOMPLETE output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('RETRY output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('files with PASSED', $output->getOutput());
        $this->assertOutputOrder($output, [
            'files with ABNORMAL TERMINATIONS',
            'files with COVERAGE NOT FETCHED',
            'files with ERRORS',
            'files with FAILURES',
            'files with WARNING',
            'files with NO TESTS EXECUTED',
            'files with RISKY',
            'files with RETRIED',
        ]);
    }

    public function testRegressionDuplicateFilesDueToMethodNames(): void
    {
        $this->setTextFilter('PassThenRetryTestStub.php');
        $this->loadContainer();

        $output = $this->getConsoleOutput();
        $runner = $this->getService(Runner::class);
        $this->assertInstanceOf(Runner::class, $runner);
        $this->assertNotEquals(0, $runner->run());

        $this->assertOutputOrder($output, [
            'Errors output',
            PassThenRetryTestStub::class . '::testBrokenTest',
            PassThenRetryTestStub::class . '::testFail',
            'files with ERRORS',
            PassThenRetryTestStub::class . PHP_EOL,
            'files with FAILURES',
            PassThenRetryTestStub::class . PHP_EOL,
            'files with RETRIED',
            PassThenRetryTestStub::class . PHP_EOL,
        ]);
    }
}
