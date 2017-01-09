<?php

namespace Tests;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\File\Cleaner;
use Paraunit\File\TempDirectory;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Parser\JSON\LogParser;
use Prophecy\Argument;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\StubbedParaunitProcess;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class BaseFunctionalTestCase
 * @package Paraunit\Tests
 */
abstract class BaseFunctionalTestCase extends BaseTestCase
{
    /** @var ContainerBuilder */
    protected $container;

    /** @var  ParallelConfiguration */
    protected $configuration;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->configuration = new ParallelConfiguration();
    }

    protected function setUp()
    {
        parent::setUp();

        $this->loadContainer();

        $this->cleanUpTempDirForThisExecution();
    }

    protected function tearDown()
    {
        $this->cleanUpTempDirForThisExecution();

        parent::tearDown();
    }

    /**
     * @param StubbedParaunitProcess $process
     * @param string $stubLog
     */
    public function createLogForProcessFromStubbedLog(StubbedParaunitProcess $process, $stubLog)
    {
        $stubLogFilename = __DIR__ . '/Stub/PHPUnitJSONLogOutput/' . $stubLog . '.json';
        $this->assertFileExists($stubLogFilename, 'Stub log file missing! ' . $stubLogFilename);

        /** @var TempFilenameFactory $filenameService */
        $filenameService = $this->container->get('paraunit.configuration.temp_filename_factory');
        $filename = $filenameService->getFilenameForLog($process->getUniqueId());

        copy($stubLogFilename, $filename);
    }

    protected function cleanUpTempDirForThisExecution()
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
            $this->assertNotFalse($position, 'String not found: ' . $string . $output->getOutput());
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
        /** @var LogParser $logParser */
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

    protected function loadContainer()
    {
        $input = $this->prophesize('Symfony\Component\Console\Input\InputInterface');
        $input->getOption('parallel')
            ->willReturn(10);
        $input->getOption(Argument::cetera())
            ->willReturn(null);

        $this->container = $this->configuration->buildContainer($input->reveal());
    }
}
