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
use Paraunit\Process\ProcessFactoryInterface;
use Paraunit\Runner\Runner;
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
            ProcessPrinter::class,
            ProcessFactoryInterface::class,
            Runner::class,
            EventDispatcherInterface::class,
            'paraunit.test_result.success_container',
            'paraunit.test_result.success_format',
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
        $this->assertInstanceOf(DebugPrinter::class, $service);
        $this->assertInstanceOf(EventSubscriberInterface::class, $service);
    }

    private function getService(ContainerBuilder $container, string $serviceName): ?object
    {
        return $container->get(sprintf(ParallelConfiguration::PUBLIC_ALIAS_FORMAT, $serviceName));
    }
}
