<?php

declare(strict_types=1);

namespace Tests\Functional\Printer;

use Paraunit\Printer\FailuresPrinter;
use Tests\BaseFunctionalTestCase;

class FailuresPrinterTest extends BaseFunctionalTestCase
{
    public function testOnEngineEndPrintsInTheRightOrder(): void
    {
        $this->processAllTheStubLogs();

        /** @var FailuresPrinter $printer */
        $printer = $this->getService(FailuresPrinter::class);

        $printer->onEngineEnd();

        $output = $this->getConsoleOutput();

        $this->assertNotEmpty($output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('PASSED output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('SKIPPED output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('INCOMPLETE output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('files with PASSED', $output->getOutput());
        $this->assertOutputOrder($output, [
            'Abnormal Terminations (fatal Errors, Segfaults) output:',
            'Errors output:',
            'Failures output:',
            'Warnings output:',
            'Risky Outcome output:',
        ]);
    }
}
