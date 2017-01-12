<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\CoverageConfiguration;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;

/**
 * Class ParaunitCoverageTest
 * @package Tests\Unit\Configuration
 */
class CoverageConfigurationTest extends BaseUnitTestCase
{
    public function testBuildContainer()
    {
        $paraunit = new CoverageConfiguration();
        $input = $this->prophesize('Symfony\Component\Console\Input\InputInterface');
        $input->getOption('parallel')
            ->willReturn(10);
        $input->getOption(Argument::cetera())
            ->willReturn(null);

        $container = $paraunit->buildContainer($input->reveal());

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $container);

        $this->assertTrue($container->hasParameter('paraunit.max_process_count'), 'Process limit parameter missing');
        $this->assertEquals(10, $container->getParameter('paraunit.max_process_count'));

        $this->assertTrue($container->hasParameter('paraunit.max_process_count'), 'Process limit parameter missing');
        $this->assertEquals(10, $container->getParameter('paraunit.max_process_count'));

        $requiredDefinitions = array(
            'paraunit.parser.json_log_parser',
            'paraunit.printer.process_printer',
            'paraunit.process.process_factory',
            'paraunit.runner.runner',
            'event_dispatcher',
            'paraunit.test_result.test_result_factory',
            'paraunit.test_result.pass_container',
            'paraunit.test_result.pass_test_result_format',

            'paraunit.coverage.coverage_fetcher',
            'paraunit.coverage.coverage_merger',
            'paraunit.coverage.coverage_result',
            'paraunit.configuration.phpdbg_bin_file',
            'paraunit.printer.coverage_printer',
        );

        $servicesIds = $container->getServiceIds();

        foreach ($requiredDefinitions as $definition) {
            $this->assertContains($definition, $servicesIds);
            $container->get($definition); // test instantiation, to prevent misconfigurations
        }

        $this->markTestIncomplete('Awaiting #29 -- paraunit.printer.debug_printer');
    }

    /**
     * @dataProvider cliOptionsProvider
     */
    public function testBuildContainerWithCoverageSettings($inputOption, $processorClass)
    {
        $paraunit = new CoverageConfiguration();
        $input = $this->prophesize('Symfony\Component\Console\Input\InputInterface');
        $options = array(
            'clover',
            'xml',
            'html',
            'text',
            'text-to-console',
        );

        foreach ($options as $optionName) {
            $input->getOption($optionName)
                ->willReturn($optionName === $inputOption ? 'someValue' : null);
        }

        $input->getOption('parallel')
            ->shouldBeCalled()
            ->willReturn(10);

        $container = $paraunit->buildContainer($input->reveal());

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $container);

        $coverageResult = $container->get('paraunit.coverage.coverage_result');
        $reflection = new \ReflectionObject($coverageResult);
        $property = $reflection->getProperty('coverageProcessors');
        $property->setAccessible(true);
        $processors = $property->getValue($coverageResult);

        $this->assertCount(1, $processors, 'Wrong count of coverage processors');
        $this->assertInstanceOf($processorClass, $processors[0]);
    }

    public function cliOptionsProvider()
    {
        return array(
            array('clover', 'Paraunit\Coverage\Processor\Clover'),
            array('xml', 'Paraunit\Coverage\Processor\Xml'),
            array('html', 'Paraunit\Coverage\Processor\Html'),
            array('text', 'Paraunit\Coverage\Processor\Text'),
            array('text-to-console', 'Paraunit\Coverage\Processor\TextToConsole'),
        );
    }
}
