<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\ParallelCoverageConfiguration;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;

/**
 * Class ParaunitCoverageTest
 * @package Tests\Unit\Configuration
 */
class ParallelCoverageConfigurationTest extends BaseUnitTestCase
{
    public function testBuildContainer()
    {
        $paraunit = new ParallelCoverageConfiguration();
        $input = $this->prophesize('Symfony\Component\Console\Input\InputInterface');
        $input->getOption('parallel')
            ->willReturn(10);
        $input->getOption(Argument::cetera())
            ->willReturn(null);

        $container = $paraunit->buildContainer($input->reveal());

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $container);

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
            'paraunit.coverage.coverage_output_paths',
            'paraunit.printer.coverage_printer',
        );

        $servicesIds = $container->getServiceIds();

        foreach ($requiredDefinitions as $definition) {
            $this->assertContains($definition, $servicesIds);
            $container->get($definition); // test instantiation, to prevent misconfigurations
        }

        $this->markTestIncomplete('Awaiting #29 -- paraunit.printer.debug_printer');
    }

    public function testBuildContainerWithParameter()
    {
        $paraunit = new ParallelCoverageConfiguration();
        $input = $this->prophesize('Symfony\Component\Console\Input\InputInterface');
        $input->getOption('clover')->willReturn('coverage.clover.xml');
        $input->getOption('xml')->willReturn('coverage.xml');
        $input->getOption('html')->willReturn('coverage/html');
        $input->getOption('parallel')
            ->shouldBeCalled()
            ->willReturn(10);

        $container = $paraunit->buildContainer($input->reveal());

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $container);
        $this->assertEquals('coverage.clover.xml', $container->getParameter('paraunit.coverage.clover_file_path'));
        $this->assertEquals('coverage.xml', $container->getParameter('paraunit.coverage.xml_file_path'));
        $this->assertEquals('coverage/html', $container->getParameter('paraunit.coverage.html_path'));
    }
}
