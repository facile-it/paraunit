<?php

namespace Tests;

use Paraunit\Configuration\JSONLogFilename;
use Paraunit\Configuration\Paraunit;
use Paraunit\File\Cleaner;
use Paraunit\File\TempDirectory;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Parser\JSONLogParser;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\StubbedParaunitProcess;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class BaseFunctionalTestCase
 * @package Paraunit\Tests
 */
abstract class BaseFunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder */
    protected $container = null;

    protected function setUp()
    {
        parent::setUp();

        $this->container = Paraunit::buildContainer();
        $this->cleanUpTempDirForThisExecution();
    }

    protected function tearDown()
    {
        $this->cleanUpTempDirForThisExecution();

        parent::tearDown();
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function createLogForProcessFromStubbedLog(StubbedParaunitProcess $process, $stubLog)
    {
        $stubLogFilename = __DIR__ . '/Stub/PHPUnitJSONLogOutput/' . $stubLog . '.json';
        $this->assertTrue(file_exists($stubLogFilename), 'Stub log file missing! ' . $stubLogFilename);

        /** @var JSONLogFilename $filename */
        $filenameService = $this->container->get('paraunit.configuration.json_log_filename');
        $filename = $filenameService->generate($process);

        copy($stubLogFilename, $filename);
    }

    private function cleanUpTempDirForThisExecution()
    {
        if ($this->container) {
            /** @var TempDirectory $tempDirectory */
            $tempDirectory = $this->container->get('paraunit.file.temp_directory');
            Cleaner::cleanUpDir($tempDirectory->getTempDirForThisExecution());
        }
    }

    protected function assertOutputOrder(UnformattedOutputStub $output, array $strings)
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

    protected function processAllTheStubLogs()
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
