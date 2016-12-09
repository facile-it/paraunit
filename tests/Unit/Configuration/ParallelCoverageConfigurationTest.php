<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\ParallelCoverageConfiguration;
use Prophecy\Argument;

/**
 * Class ParaunitCoverageTest
 * @package Tests\Unit\Configuration
 */
class ParallelCoverageConfigurationTest extends \PHPUnit_Framework_TestCase
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

        $servicesIds = $container->getServiceIds();
        $this->assertContains('paraunit.file.cleaner', $servicesIds);
        $this->assertContains('paraunit.parser.json_log_parser', $servicesIds);
        $this->assertContains('paraunit.printer.process_printer', $servicesIds);
        $this->assertContains('paraunit.process.process_factory', $servicesIds);
        $this->assertContains('paraunit.runner.runner', $servicesIds);
        $this->assertContains('event_dispatcher', $servicesIds);
        $this->assertContains('paraunit.test_result.test_result_factory', $servicesIds);
        $this->assertContains('paraunit.test_result.pass_container', $servicesIds);
        $this->assertContains('paraunit.test_result.pass_test_result_format', $servicesIds);

        $this->assertContains('paraunit.coverage.coverage_fetcher', $servicesIds);
        $this->assertContains('paraunit.coverage.coverage_merger', $servicesIds);
        $this->assertContains('paraunit.coverage.coverage_result', $servicesIds);
        $this->assertContains('paraunit.configuration.phpdbg_bin_file', $servicesIds);
        $this->assertContains('paraunit.coverage.coverage_output_paths', $servicesIds);

        $this->markTestIncomplete('Awaiting #29');
        $this->assertContains('paraunit.printer.debug_printer', $servicesIds);
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
