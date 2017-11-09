<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\File\Cleaner;
use Paraunit\Parser\JSON\LogParser;
use Paraunit\Printer\CoveragePrinter;
use Paraunit\Printer\DebugPrinter;
use Paraunit\Printer\ProcessPrinter;
use Paraunit\Process\ProcessBuilderFactory;
use Paraunit\Runner\Runner;
use Paraunit\TestResult\TestResultFactory;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tests\BaseUnitTestCase;

/**
 * Class ParaunitTest
 * @package Tests\Unit\Configuration
 */
class ParallelConfigurationTest extends BaseUnitTestCase
{
    public function testBuildContainer()
    {
        $paraunit = new ParallelConfiguration();
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

        $container = $paraunit->buildContainer($input->reveal(), $output->reveal());

        $this->assertInstanceOf(ContainerBuilder::class, $container);

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
            ProcessPrinter::class,
            ProcessBuilderFactory::class,
            Runner::class,
            EventDispatcherInterface::class,
            TestResultFactory::class,
            'paraunit.test_result.pass_container',
            'paraunit.test_result.pass_format',
            PHPUnitConfig::class,
        ];

        $servicesIds = $container->getServiceIds();
        $this->assertNotContains(PHPDbgBinFile::class, $servicesIds);
        $this->assertNotContains(CoverageFetcher::class, $servicesIds);
        $this->assertNotContains(CoveragePrinter::class, $servicesIds);

        foreach ($requiredDefinitions as $definition) {
            $container->get($definition); // test instantiation, to prevent misconfigurations
        }
    }

    public function testBuildContainerWithDebug()
    {
        $paraunit = new ParallelConfiguration();
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);
        $input->getArgument('stringFilter')
            ->willReturn('text');
        $input->getOption('debug')
            ->willReturn(true);
        $input->getOption(Argument::cetera())
            ->willReturn(null);

        $container = $paraunit->buildContainer($input->reveal(), $output->reveal());

        $this->assertInstanceOf(ContainerBuilder::class, $container);

        $service = $container->get(DebugPrinter::class); // test instantiation, to prevent misconfigurations
        $this->assertInstanceOf(DebugPrinter::class, $service);
        $this->assertInstanceOf(EventSubscriberInterface::class, $service);
    }
}
