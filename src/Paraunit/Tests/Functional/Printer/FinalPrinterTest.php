<?php

namespace Paraunit\Tests\Functional\Printer;


use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Parser\JSONLogParser;
use Paraunit\Parser\OutputContainerBearerInterface;
use Paraunit\Printer\FinalPrinter;
use Paraunit\Tests\BaseFunctionalTestCase;
use Paraunit\Tests\Stub\UnformattedOutputStub;
use Paraunit\Tests\Stub\StubbedParaProcess;

/**
 * Class FinalPrinterTest
 * @package Paraunit\Tests\Functional\Printer
 */
class FinalPrinterTest extends BaseFunctionalTestCase
{
    public function testOnEngineEndPrintsTheRightCountSummary()
    {
        $process = new StubbedParaProcess();
        $process->addTestResult('.');
        $process->addTestResult('.');
        $process->addTestResult('.');

        $output = new UnformattedOutputStub();
        $context = array(
            'start' => new \DateTime('-1 minute'),
            'end' => new \DateTime(),
            'process_completed' => array_fill(0, 15, $process),
        );
        $engineEvent = new EngineEvent($output, $context);

        /** @var JSONLogParser $logParser */
        $logParser = $this->container->get('paraunit.parser.json_log_parser');

        foreach ($logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof OutputContainerBearerInterface) {
                $parser->getOutputContainer()->addToOutputBuffer($process, 'Test');
            }
        }

        /** @var FinalPrinter $printer */
        $printer = $this->container->get('paraunit.printer.final_printer');

        $printer->onEngineEnd($engineEvent);

        $this->assertContains('Execution time -- 00:01:00', $output->getOutput());
        $this->assertContains('Executed: 15 test classes, 45 tests', $output->getOutput());
    }

    public function testOnEngineEndPrintsInTheRightOrder()
    {
        $output = new UnformattedOutputStub();
        $process = new StubbedParaProcess();
        $context = array(
            'start' => new \DateTime('-1 minute'),
            'end' => new \DateTime(),
            'process_completed' => array($process),
        );
        $engineEvent = new EngineEvent($output, $context);

        /** @var JSONLogParser $logParser */
        $logParser = $this->container->get('paraunit.parser.json_log_parser');

        $logParser->getAbnormalTerminatedOutputContainer()->addToOutputBuffer($process, 'Test');
        foreach ($logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof OutputContainerBearerInterface) {
                $parser->getOutputContainer()->addToOutputBuffer($process, 'Test');
            }
        }

        /** @var FinalPrinter $printer */
        $printer = $this->container->get('paraunit.printer.final_printer');

        $printer->onEngineEnd($engineEvent);

        $this->assertNotEmpty($output->getOutput());
        $this->assertOutputOrder($output, array(
            'Abnormal Terminations (fatal Errors, Segfaults) output:',
            'Errors output:',
            'Failures output:',
            'Warnings output:',
            'Risky Outcome output:',
            'Skipped Outcome output:',
            'Incomplete Outcome output:',
            'files with ABNORMAL TERMINATIONS (FATAL ERRORS, SEGFAULTS)',
            'files with ERRORS',
            'files with FAILURES',
            'files with WARNING',
            'files with RISKY',
            'files with SKIP',
            'files with INCOMPLETE'
        ));
    }

    private function assertOutputOrder(UnformattedOutputStub $output, array $strings)
    {
        $previousPosition = 0;
        $previousString = '<beginning of output>';
        foreach ($strings as $string) {
            $position = strpos($output->getOutput(), $string);
            $this->assertNotSame(false, $position, 'String not found: ' . $string . $output->getOutput());
            $this->assertGreaterThan(
                $previousPosition,
                $position,
                'Failed asserting that "' . $string . '" comes before "' . $previousString . '"'
            );
            $previousString = $string;
            $previousPosition = $position;
        }
    }
}
