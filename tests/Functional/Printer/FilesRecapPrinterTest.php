<?php

namespace Tests\Functional\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Printer\FilesRecapPrinter;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\StubbedParaunitProcess;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class FilesRecapPrinterTest
 * @package Tests\Functional\Printer
 */
class FilesRecapPrinterTest extends BaseFunctionalTestCase
{
    public function testOnEngineEndPrintsInTheRightOrder()
    {
        $output = new UnformattedOutputStub();
        $process = new StubbedParaunitProcess();
        $context = array(
            'start' => new \DateTime('-1 minute'),
            'end' => new \DateTime(),
            'process_completed' => array($process),
        );

        $this->processAllTheStubLogs();

        /** @var FilesRecapPrinter $printer */
        $printer = $this->container->get('paraunit.printer.files_recap_printer');

        $printer->onEngineEnd(new EngineEvent($output, $context));

        $this->assertNotEmpty($output->getOutput());
        $this->assertNotContains('PASSED output', $output->getOutput(), null, true);
        $this->assertNotContains('SKIPPED output', $output->getOutput(), null, true);
        $this->assertNotContains('INCOMPLETE output', $output->getOutput(), null, true);
        $this->assertNotContains('files with PASSED', $output->getOutput(), null, true);
        $this->assertOutputOrder($output, array(
            'files with UNKNOWN',
            'files with ERRORS',
            'files with FAILURES',
            'files with WARNING',
            'files with RISKY',
            'files with SKIP',
            'files with INCOMPLETE'
        ));
    }
}
