<?php

namespace Tests\Functional\Printer;


use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Parser\JSONLogParser;
use Paraunit\Parser\OutputContainerBearerInterface;
use Paraunit\Printer\FinalPrinter;
use Paraunit\TestResult\NullTestResult;
use Paraunit\TestResult\TestResultContainer;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\StubbedParaunitProcess;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class FinalPrinterTest
 * @package Tests\Functional\Printer
 */
class FinalPrinterTest extends BaseFunctionalTestCase
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

        /** @var FinalPrinter $printer */
        $printer = $this->container->get('paraunit.printer.final_printer');

        $printer->onEngineEnd(new EngineEvent($output, $context));

        $this->assertNotEmpty($output->getOutput());
        $this->assertNotContains('PASSED output', $output->getOutput());
        $this->assertNotContains('SKIPPED output', $output->getOutput());
        $this->assertNotContains('INCOMPLETE output', $output->getOutput());
        $this->assertOutputOrder($output, array(
            'Unknown',
            'Abnormal Terminations (fatal Errors, Segfaults) output:',
            'Errors output:',
            'Failures output:',
            'Warnings output:',
            'Risky Outcome output:',
            'Skipped Outcome output:',
            'Incomplete Outcome output:',
            'files with UNKNOWN',
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
                'Failed asserting that "' . $string . '" comes after "' . $previousString . '"'
            );
            $previousString = $string;
            $previousPosition = $position;
        }
    }

    private function processAllTheStubLogs()
    {
        /** @var JSONLogParser $logParser */
        $logParser = $this->container->get('paraunit.parser.json_log_parser');

        $logsToBeProcessed = array(
            JSONLogStub::TWO_ERRORS_TWO_FAILURES,
            JSONLogStub::ALL_GREEN,
            JSONLogStub::ONE_ERROR,
            JSONLogStub::ONE_INCOMPLETE,
            JSONLogStub::ONE_RISKY,
            JSONLogStub::ONE_SKIP,
            JSONLogStub::ONE_WARNING,
            JSONLogStub::FATAL_ERROR,
            JSONLogStub::SEGFAULT,
            JSONLogStub::UNKNOWN,
        );

        $process = new StubbedParaunitProcess();
        $processEvent = new ProcessEvent($process);

        foreach ($logsToBeProcessed as $logName) {
            $process->setFilename($logName . '.php');
            $this->createLogForProcessFromStubbedLog($process, $logName);
            $logParser->onProcessTerminated($processEvent);
        }
    }
}
