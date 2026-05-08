<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\DependencyInjection\ParallelContainerDefinition;
use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\File\Cleaner;
use Paraunit\Filter\RandomizeList;
use Paraunit\Filter\TestList;
use Paraunit\Logs\JSON\LogParser;
use Paraunit\Printer\CoveragePrinter;
use Paraunit\Printer\DebugPrinter;
use Paraunit\Printer\ProgressPrinter;
use Paraunit\Process\ProcessFactory;
use Paraunit\Runner\Runner;
use Paraunit\Runner\RunnerConfiguration;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tests\BaseUnitTestCase;

class ParallelConfigurationTest extends BaseUnitTestCase
{
    public function testBuildContainer(): void
    {
        $paraunit = new ParallelConfiguration(true);
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);
        $input->getArgument('stringFilter')
            ->willReturn('text');
        $input->getOption('parallel')
            ->willReturn(10);
        $input->getOption('chunk-size')
            ->willReturn(1);
        $input->getOption('testsuite')
            ->willReturn('testsuite');
        $input->getOption('configuration')
            ->willReturn($this->getConfigForStubs());
        $input->getOption(Argument::cetera())
            ->willReturn(null);

        $container = $paraunit->buildContainer($input->reveal(), $output->reveal());

        $requiredParameters = [
            'paraunit.max_process_count' => 10,
            'paraunit.testsuite' => 'testsuite',
            'paraunit.string_filter' => 'text',
            'paraunit.phpunit_config_filename' => $this->getConfigForStubs(),
        ];

        foreach ($requiredParameters as $parameterName => $expectedValue) {
            $this->assertTrue($container->hasParameter($parameterName), 'Parameter missing: ' . $parameterName);
            $this->assertEquals($expectedValue, $container->getParameter($parameterName));
        }

        $requiredDefinitions = [
            OutputInterface::class,
            Cleaner::class,
            LogParser::class,
            ProgressPrinter::class,
            ProcessFactory::class,
            Runner::class,
            RunnerConfiguration::class,
            EventDispatcherInterface::class,
            TestResultContainer::class,
            PHPUnitConfig::class,
        ];

        $servicesIds = $container->getServiceIds();
        $this->assertNotContains(PHPDbgBinFile::class, $servicesIds);
        $this->assertNotContains(CoverageFetcher::class, $servicesIds);
        $this->assertNotContains(CoveragePrinter::class, $servicesIds);

        foreach ($requiredDefinitions as $definitionName) {
            $this->getService($container, $definitionName); // test instantiation, to prevent misconfiguration
        }
    }

    public function testBuildContainerWithDebug(): void
    {
        $paraunit = new ParallelConfiguration(true);
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);
        $input->getArgument('stringFilter')
            ->willReturn('text');
        $input->getOption('debug')
            ->willReturn(true);
        $input->getOption(Argument::cetera())
            ->willReturn(null);

        $container = $paraunit->buildContainer($input->reveal(), $output->reveal());

        // test instantiation, to prevent misconfiguration
        $service = $this->getService($container, DebugPrinter::class);
        $this->assertInstanceOf(EventSubscriberInterface::class, $service);
        $this->assertInstanceOf(DebugPrinter::class, $service);
    }

    public function testBuildContainerWithSortRandom(): void
    {
        $paraunit = new ParallelConfiguration(true);
        $input = $this->prophesize(InputInterface::class);
        $input->getArgument('stringFilter')
            ->willReturn('text');
        $input->getOption('sort')
            ->willReturn('random');
        $input->getOption(Argument::cetera())
            ->willReturn(null);

        $container = $paraunit->buildContainer($input->reveal(), $this->prophesize(OutputInterface::class)->reveal());

        $service = $this->getService($container, TestList::class);
        $this->assertInstanceOf(RandomizeList::class, $service);
    }

    public function testBuildContainerWithSortInvalid(): void
    {
        $paraunit = new ParallelConfiguration(true);
        $input = $this->prophesize(InputInterface::class);
        $input->getArgument('stringFilter')
            ->willReturn('text');
        $input->getOption('sort')
            ->willReturn('foo');
        $input->getOption(Argument::cetera())
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $paraunit->buildContainer($input->reveal(), $this->prophesize(OutputInterface::class)->reveal());
    }

    public function testRunnerConfigurationWithNoStopOptionsYieldsNoSetStopOnCalls(): void
    {
        $container = $this->buildContainerForRunnerConfigurationAssertions();

        $this->assertSame([], $this->getSetStopOnArguments($container));
    }

    public function testRunnerConfigurationWithStopOnDefect(): void
    {
        $container = $this->buildContainerForRunnerConfigurationAssertions(['stop-on-defect' => true]);

        $expected = [
            TestIssue::Deprecation,
            TestIssue::Risky,
            TestIssue::Warning,
            TestOutcome::AbnormalTermination,
            TestOutcome::Error,
            TestOutcome::Failure,
        ];

        $this->assertEqualsCanonicalizing($expected, $this->getSetStopOnArguments($container));
    }

    /**
     * @return \Generator<string, array{0: string, 1: list<TestIssue|TestOutcome>}>
     */
    public static function granularStopOnOptionsProvider(): \Generator
    {
        foreach (TestIssue::cases() as $case) {
            $option = match ($case) {
                TestIssue::Deprecation => 'stop-on-deprecation',
                TestIssue::PHPUnitDeprecation => 'stop-on-phpunit-deprecation',
                TestIssue::Risky => 'stop-on-risky',
                TestIssue::Warning => 'stop-on-warning',
                TestIssue::Notice => 'stop-on-notice',
                TestIssue::CoverageFailure => null,
            };
            if ($option !== null) {
                yield 'issue_' . $case->name => [$option, [$case]];
            }
        }

        yield 'outcomes_stop_on_error' => ['stop-on-error', [TestOutcome::AbnormalTermination, TestOutcome::Error]];

        foreach (TestOutcome::cases() as $case) {
            if ($case === TestOutcome::AbnormalTermination || $case === TestOutcome::Error) {
                continue;
            }

            $option = match ($case) {
                TestOutcome::Failure => 'stop-on-failure',
                TestOutcome::Skipped => 'stop-on-skipped',
                TestOutcome::Incomplete => 'stop-on-incomplete',
                default => null,
            };

            if ($option !== null) {
                yield 'outcome_' . $case->name => [$option, [$case]];
            }
        }
    }

    /**
     * @param list<TestIssue|TestOutcome> $expectedEnums
     */
    #[DataProvider('granularStopOnOptionsProvider')]
    public function testRunnerConfigurationGranularStopOnOption(string $option, array $expectedEnums): void
    {
        $container = $this->buildContainerForRunnerConfigurationAssertions([$option => true]);

        $this->assertEqualsCanonicalizing($expectedEnums, $this->getSetStopOnArguments($container));
    }

    public function testRunnerConfigurationStopOnDefectAndGranularAreAdditive(): void
    {
        $container = $this->buildContainerForRunnerConfigurationAssertions([
            'stop-on-defect' => true,
            'stop-on-skipped' => true,
        ]);

        $expected = [
            TestIssue::Deprecation,
            TestIssue::Risky,
            TestIssue::Warning,
            TestOutcome::AbnormalTermination,
            TestOutcome::Error,
            TestOutcome::Failure,
            TestOutcome::Skipped,
        ];

        $this->assertEqualsCanonicalizing($expected, $this->getSetStopOnArguments($container));
    }

    /**
     * Builds an uncompiled {@see ContainerBuilder} with the same CLI wiring as
     * {@see ParallelConfiguration::buildContainer()} up to and including {@see ParallelConfiguration::loadCommandLineOptions()}.
     *
     * @param array<string, mixed> $optionOverrides
     */
    private function buildContainerForRunnerConfigurationAssertions(array $optionOverrides = []): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new ParallelContainerDefinition())->configure($container);

        $input = $this->prophesizeInputForParallelConfigurationBuild($optionOverrides);

        $parallelConfiguration = new ParallelConfiguration(true);
        $loadCommandLineOptions = new \ReflectionMethod(ParallelConfiguration::class, 'loadCommandLineOptions');
        $loadCommandLineOptions->invoke($parallelConfiguration, $container, $input);

        return $container;
    }

    /**
     * @param array<string, mixed> $optionOverrides
     */
    private function prophesizeInputForParallelConfigurationBuild(array $optionOverrides = []): InputInterface
    {
        $defaults = [
            'parallel' => 10,
            'chunk-size' => 1,
            'testsuite' => 'testsuite',
            'configuration' => $this->getConfigForStubs(),
            'logo' => false,
            'pass-through' => null,
            'sort' => null,
            'exclude-testsuite' => null,
            'test-suffix' => null,
            'debug' => false,
            'display-all-issues' => false,
            'display-deprecations' => false,
            'display-notices' => false,
            'display-phpunit-deprecations' => false,
            'display-warnings' => false,
            'stop-on-defect' => false,
            'stop-on-error' => false,
            'stop-on-failure' => false,
            'stop-on-warning' => false,
            'stop-on-risky' => false,
            'stop-on-deprecation' => false,
            'stop-on-phpunit-deprecation' => false,
            'stop-on-notice' => false,
            'stop-on-skipped' => false,
            'stop-on-incomplete' => false,
        ];

        $input = $this->prophesize(InputInterface::class);
        $input->getArgument('stringFilter')->willReturn('text');

        foreach (array_merge($defaults, $optionOverrides) as $name => $value) {
            $input->getOption((string) $name)->willReturn($value);
        }

        return $input->reveal();
    }

    /**
     * @return list<TestIssue|TestOutcome>
     */
    private function getSetStopOnArguments(ContainerBuilder $container): array
    {
        $definition = $container->getDefinition(RunnerConfiguration::class);
        $arguments = [];
        foreach ($definition->getMethodCalls() as [$method, $args]) {
            if ($method === 'setStopOn') {
                $arguments[] = $args[0];
            }
        }

        return $arguments;
    }

    private function getService(ContainerBuilder $container, string $serviceName): object
    {
        return $container->get(sprintf(ParallelConfiguration::PUBLIC_ALIAS_FORMAT, $serviceName));
    }
}
