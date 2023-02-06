<?php

declare(strict_types=1);

namespace Tests;

use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\File\Cleaner;
use Paraunit\File\TempDirectory;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tests\Stub\StubbedParaunitProcess;
use Tests\Stub\UnformattedOutputStub;

abstract class BaseIntegrationTestCase extends BaseTestCase
{
    private ?ContainerBuilder $container = null;

    protected ParallelConfiguration $configuration;

    /** @var string */
    protected $textFilter;

    /** @var string[] */
    private array $options = [];

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->configuration = new ParallelConfiguration(true);
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
        if ($this->container !== null) {
            /** @var TempDirectory $tempDirectory */
            $tempDirectory = $this->getService(TempDirectory::class);
            Cleaner::cleanUpDir($tempDirectory->getTempDirForThisExecution());
        }
    }

    /**
     * @param string[] $strings
     */
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

    protected function populateTestResultContainerWithAllPossibleStatuses(): void
    {
        $allPossibleStatuses = [...TestIssue::cases(), ...TestOutcome::cases()];
        $testResulContainer = $this->getService(TestResultContainer::class);

        foreach ($allPossibleStatuses as $status) {
            $testResult = new TestResultWithMessage(new Test('FooTest'), $status, 'Stub message per status ' . $status->value);
            $testResulContainer->addTestResult($testResult);
        }
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $serviceName
     *
     * @return T
     */
    public function getService(string $serviceName): object
    {
        if ($this->container === null) {
            throw new \RuntimeException('Container not ready');
        }

        $service = $this->container->get(sprintf(ParallelConfiguration::PUBLIC_ALIAS_FORMAT, $serviceName));

        $this->assertInstanceOf($serviceName, $service, 'Service not found: ' . $serviceName);

        return $service;
    }

    protected function getParameter(string $parameterName): bool|int|float|string
    {
        if ($this->container !== null) {
            $unitEnum = $this->container->getParameter($parameterName);
            $this->assertIsScalar($unitEnum);

            return $unitEnum;
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
        $input->getOption('chunk-size')
            ->willReturn(1);
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
