<?php

declare(strict_types=1);

namespace Tests;

use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\File\Cleaner;
use Paraunit\File\TempDirectory;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Parser\JSON\LogParser;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\StubbedParaunitProcess;
use Tests\Stub\UnformattedOutputStub;

abstract class BaseIntegrationTestCase extends BaseTestCase
{
    /** @var ContainerBuilder|null */
    private $container;

    /** @var ParallelConfiguration */
    protected $configuration;

    /** @var string */
    protected $textFilter;

    /** @var string[] */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->configuration = new ParallelConfiguration(true);
        $this->options = [];
        $this->setOption('configuration', $this->getStubPath() . DIRECTORY_SEPARATOR . 'phpunit_for_stubs.xml');
    }

    protected function setup(): void
    {
        parent::setUp();

        $this->cleanUpTempDirForThisExecution();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTempDirForThisExecution();

        parent::tearDown();
    }

    protected function createLogForProcessFromStubbedLog(StubbedParaunitProcess $process, string $stubLog): void
    {
        $stubLogFilename = __DIR__ . '/Stub/PHPUnitJSONLogOutput/' . $stubLog . '.json';
        $this->assertFileExists($stubLogFilename, 'Stub log file missing! ' . $stubLogFilename);

        /** @var TempFilenameFactory $filenameService */
        $filenameService = $this->getService(TempFilenameFactory::class);
        $filename = $filenameService->getFilenameForLog($process->getUniqueId());

        copy($stubLogFilename, $filename);
    }

    protected function cleanUpTempDirForThisExecution(): void
    {
        if ($this->container) {
            /** @var TempDirectory $tempDirectory */
            $tempDirectory = $this->getService(TempDirectory::class);
            Cleaner::cleanUpDir($tempDirectory->getTempDirForThisExecution());
        }
    }

    protected function assertOutputOrder(UnformattedOutputStub $output, array $strings): void
    {
        $previousPosition = 0;
        $previousString = '<beginning of output>';
        foreach ($strings as $string) {
            /** @var int $position */
            $position = strpos($output->getOutput(), $string, $previousPosition);
            $this->assertNotFalse($position, $output->getOutput() . PHP_EOL . 'String not found: ' . $string);
            $this->assertGreaterThan(
                $previousPosition,
                $position,
                'Failed asserting that "' . $string . '" comes after "' . $previousString . '"' . $output->getOutput()
            );
            $previousString = $string;
            $previousPosition = $position;
        }
    }

    protected function processAllTheStubLogs(): void
    {
        /** @var LogParser $logParser */
        $logParser = $this->getService(LogParser::class);

        $logsToBeProcessed = [
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
        ];

        $process = new StubbedParaunitProcess();
        $processEvent = new ProcessTerminated($process);

        foreach ($logsToBeProcessed as $logName) {
            $process->setFilename($logName . '.php');
            $this->createLogForProcessFromStubbedLog($process, $logName);
            $logParser->onProcessTerminated($processEvent);
        }
    }

    /**
     * @return object
     */
    public function getService(string $serviceName)
    {
        if (! $this->container) {
            throw new \RuntimeException('Container not ready');
        }

        $service = $this->container->get(sprintf(ParallelConfiguration::PUBLIC_ALIAS_FORMAT, $serviceName));

        if (! $service) {
            throw new \RuntimeException('Service not found: ' . $serviceName);
        }

        return $service;
    }

    public function getParameter(string $parameterName)
    {
        if ($this->container) {
            return $this->container->getParameter($parameterName);
        }

        throw new \RuntimeException('Container not ready');
    }

    protected function loadContainer(): void
    {
        $input = $this->prophesize(InputInterface::class);
        $input->getArgument('stringFilter')
            ->willReturn($this->textFilter);
        $input->getOption('parallel')
            ->willReturn(10);
        $input->getOption('logo')
            ->willReturn(false);
        $input->getOption(Argument::cetera())
            ->willReturn(null);
        $input->hasParameterOption(Argument::cetera())
            ->willReturn(false);

        foreach ($this->options as $name => $value) {
            $input->getOption($name)
                ->shouldBeCalled()
                ->willReturn($value);
        }

        $this->container = $this->configuration->buildContainer($input->reveal(), new UnformattedOutputStub());
    }

    protected function getConsoleOutput(): UnformattedOutputStub
    {
        /** @var UnformattedOutputStub $output */
        $output = $this->getService(OutputInterface::class);

        return $output;
    }

    protected function setTextFilter(string $textFilter): void
    {
        $this->textFilter = $textFilter;
    }

    protected function setOption(string $optionName, string $optionValue): void
    {
        $this->options[$optionName] = $optionValue;
    }
}
