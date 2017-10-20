<?php
declare(strict_types=1);

namespace Tests\Functional\Printer;

use Paraunit\Printer\FilesRecapPrinter;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class FilesRecapPrinterTest
 * @package Tests\Functional\Printer
 */
class FilesRecapPrinterTest extends BaseFunctionalTestCase
{
    public function testOnEngineEndPrintsInTheRightOrder()
    {
        $process = new StubbedParaunitProcess();

        $this->processAllTheStubLogs();
        $this->container->get('paraunit.test_result.no_test_executed_container')
            ->addProcessToFilenames($process);
        $this->container->get('paraunit.test_result.coverage_failure_container')
            ->addProcessToFilenames($process);

        /** @var FilesRecapPrinter $printer */
        $printer = $this->container->get(FilesRecapPrinter::class);

        $printer->onEngineEnd();

        $output = $this->getConsoleOutput();

        $this->assertNotEmpty($output->getOutput());
        $this->assertNotContains('PASSED output', $output->getOutput(), null, true);
        $this->assertNotContains('SKIPPED output', $output->getOutput(), null, true);
        $this->assertNotContains('INCOMPLETE output', $output->getOutput(), null, true);
        $this->assertNotContains('files with PASSED', $output->getOutput(), null, true);
        $this->assertOutputOrder($output, [
            'files with UNKNOWN',
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
