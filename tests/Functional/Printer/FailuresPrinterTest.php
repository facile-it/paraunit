<?php

declare(strict_types=1);

namespace Tests\Functional\Printer;

use Paraunit\Printer\FailuresPrinter;
use Paraunit\Printer\PrinterConfiguration;
use Tests\BaseFunctionalTestCase;

class FailuresPrinterTest extends BaseFunctionalTestCase
{
    public function testOnEngineEndPrintsInTheRightOrder(): void
    {
        $this->populateTestResultContainerWithAllPossibleStatuses();
        $this->getService(PrinterConfiguration::class)->setAllShouldPrint();

        $printer = $this->getService(FailuresPrinter::class);
        $printer->onEngineEnd();

        $output = $this->getConsoleOutput();
        $this->assertNotEmpty($output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('PASSED output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('SKIPPED output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('INCOMPLETE output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('RETRY output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('files with PASSED', $output->getOutput());
        $this->assertOutputOrder($output, [
            'Abnormal Terminations (fatal Errors, Segfaults) output:',
            'Coverage Not Fetched output:',
            'Errors output:',
            'Failures output:',
            'Warnings output:',
            'Deprecations output:',
            'PHPUnit Deprecations output:',
            'Risky Outcome output:',
            'Retried output:',
        ]);
    }
}
