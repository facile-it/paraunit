<?php

declare(strict_types=1);

namespace Tests\Functional\Printer;

use Paraunit\Printer\FilesRecapPrinter;
use Tests\BaseFunctionalTestCase;

class FilesRecapPrinterTest extends BaseFunctionalTestCase
{
    public function testOnEngineEndPrintsInTheRightOrder(): void
    {
        $this->processAllTheStubLogs();

        $printer = $this->getService(FilesRecapPrinter::class);
        $this->assertInstanceOf(FilesRecapPrinter::class, $printer);

        $printer->onEngineEnd();

        $output = $this->getConsoleOutput();

        $this->assertNotEmpty($output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('PASSED output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('SKIPPED output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('INCOMPLETE output', $output->getOutput());
        $this->assertStringNotContainsStringIgnoringCase('files with PASSED', $output->getOutput());
        $this->markTestIncomplete('Depends on fixing all the parsing stuff');
        $this->assertOutputOrder($output, [
            'files with ABNORMAL TERMINATIONS',
            'files with COVERAGE NOT FETCHED',
            'files with ERRORS',
            'files with FAILURES',
            'files with WARNING',
            'files with NO TESTS EXECUTED',
            'files with RISKY',
            'files with SKIP',
            'files with INCOMPLETE',
        ]);
    }
}
