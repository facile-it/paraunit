<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\Coverage\CoverageMerger;
use Paraunit\Coverage\CoverageResult;
use Paraunit\Coverage\Processor\AbstractText;
use Paraunit\Coverage\Processor\Clover;
use Paraunit\Coverage\Processor\Crap4j;
use Paraunit\Coverage\Processor\Html;
use Paraunit\Coverage\Processor\Php;
use Paraunit\Coverage\Processor\Text;
use Paraunit\Coverage\Processor\TextSummary;
use Paraunit\Coverage\Processor\Xml;
use Paraunit\Parser\JSON\LogParser;
use Paraunit\Printer\CoveragePrinter;
use Paraunit\Printer\DebugPrinter;
use Paraunit\Printer\ProcessPrinter;
use Paraunit\Process\ProcessFactoryInterface;
use Paraunit\Runner\Runner;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tests\BaseUnitTestCase;

class CoverageConfigurationTest extends BaseUnitTestCase
{
    public function testBuildContainer(): void
    {
        $paraunit = new CoverageConfiguration(true);
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);
        $input->getArgument('stringFilter')
            ->willReturn('text');
        $input->getOption('parallel')
            ->willReturn(10);
        $input->getOption('testsuite')
            ->willReturn('testsuite');
        $input->getOption('configuration')
            ->willReturn($this->getConfigForStubs());
        $input->getOption(Argument::cetera())
            ->willReturn(null);
        $input->hasParameterOption(Argument::cetera())
            ->willReturn(false);

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
            LogParser::class,
            ProcessPrinter::class,
            ProcessFactoryInterface::class,
            Runner::class,
            EventDispatcherInterface::class,
            'paraunit.test_result.success_container',
            'paraunit.test_result.success_format',

            CoverageFetcher::class,
            CoverageMerger::class,
            CoverageResult::class,
            PHPDbgBinFile::class,
            CoveragePrinter::class,
            PHPUnitConfig::class,
        ];

        foreach ($requiredDefinitions as $definitionName) {
            // test instantiation, to prevent misconfiguration
            $this->getService($container, $definitionName);
        }
    }

    public function testBuildContainerWithDebug(): void
    {
        $paraunit = new CoverageConfiguration(true);
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);
        $input->getArgument('stringFilter')
            ->willReturn('text');
        $input->getOption('debug')
            ->willReturn(true);
        $input->getOption(Argument::cetera())
            ->willReturn(null);
        $input->hasParameterOption(Argument::cetera())
            ->willReturn(false);

        $container = $paraunit->buildContainer($input->reveal(), $output->reveal());

        $service = $this->getService($container, DebugPrinter::class); // test instantiation, to prevent misconfiguration
        $this->assertInstanceOf(DebugPrinter::class, $service);
        $this->assertInstanceOf(EventSubscriberInterface::class, $service);
    }

    /**
     * @dataProvider cliOptionsProvider
     */
    public function testBuildContainerWithCoverageSettings(string $inputOption, string $processorClass): void
    {
        $paraunit = new CoverageConfiguration(true);
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);
        $options = [
            'testsuite',
            'configuration',
            'clover',
            'xml',
            'html',
            'text',
            'text-summary',
            'crap4j',
            'php',
            'ansi',
            'logo',
        ];

        foreach ($options as $optionName) {
            $input->getOption($optionName)
                ->willReturn($optionName === $inputOption ? 'someValue' : null);
            $input->hasParameterOption('--' . $optionName)
                ->willReturn($optionName === $inputOption);
        }

        $input->getArgument('stringFilter')
            ->willReturn();
        $input->getOption('parallel')
            ->shouldBeCalled()
            ->willReturn(10);
        $input->getOption('debug')
            ->willReturn(null);

        $container = $paraunit->buildContainer($input->reveal(), $output->reveal());

        $coverageResult = $this->getService($container, CoverageResult::class);
        $reflection = new \ReflectionObject($coverageResult);
        $property = $reflection->getProperty('coverageProcessors');
        $property->setAccessible(true);
        $processors = $property->getValue($coverageResult);

        $this->assertCount(1, $processors, 'Wrong count of coverage processors');
        $this->assertTrue(class_exists($processorClass));
        $this->assertInstanceOf($processorClass, $processors[0]);
    }

    /**
     * @return string[][]
     */
    public function cliOptionsProvider(): array
    {
        return [
            ['clover', Clover::class],
            ['xml', Xml::class],
            ['html', Html::class],
            ['text', Text::class],
            ['text-summary', TextSummary::class],
            ['crap4j', Crap4j::class],
            ['php', Php::class],
        ];
    }

    public function testBuildContainerWithColoredTextToConsoleCoverage(): void
    {
        $paraunit = new CoverageConfiguration(true);
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);
        $options = [
            'testsuite',
            'configuration',
            'clover',
            'xml',
            'html',
            'text',
            'crap4j',
            'php',
        ];

        foreach ($options as $optionName) {
            $input->getOption($optionName)
                ->willReturn(null);
        }

        $input->getArgument('stringFilter')
            ->willReturn();
        $input->getOption('parallel')
            ->shouldBeCalled()
            ->willReturn(10);
        $input->getOption('text-summary')
            ->shouldBeCalled()
            ->willReturn(true);
        $input->getOption('ansi')
            ->shouldBeCalled()
            ->willReturn(true);
        $input->getOption('debug')
            ->willReturn(null);
        $input->getOption('logo')
            ->willReturn(false);
        $input->hasParameterOption('--text-summary')
            ->willReturn(true);
        $input->hasParameterOption(Argument::cetera())
            ->willReturn(false);

        $container = $paraunit->buildContainer($input->reveal(), $output->reveal());

        $coverageResult = $this->getService($container, CoverageResult::class);
        $reflection = new \ReflectionObject($coverageResult);
        $property = $reflection->getProperty('coverageProcessors');
        $property->setAccessible(true);
        $processors = $property->getValue($coverageResult);

        $this->assertCount(1, $processors, 'Wrong count of coverage processors');
        $processor = $processors[0];
        $this->assertInstanceOf(TextSummary::class, $processor);

        $reflection = new \ReflectionClass(AbstractText::class);
        $property = $reflection->getProperty('showColors');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($processor));
    }

    private function getService(ContainerBuilder $container, string $serviceName)
    {
        return $container->get(sprintf(CoverageConfiguration::PUBLIC_ALIAS_FORMAT, $serviceName));
    }
}
