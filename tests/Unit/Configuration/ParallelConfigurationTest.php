<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\ParallelConfiguration;
use Prophecy\Argument;

/**
 * Class ParaunitTest
 * @package Tests\Unit\Configuration
 */
class ParallelConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildContainer()
    {
        $paraunit = new ParallelConfiguration();
        $input = $this->prophesize('Symfony\Component\Console\Input\InputInterface');
        $input->getOption('parallel')
            ->willReturn(10);
        $input->getOption(Argument::cetera())
            ->willReturn(null);

        $container = $paraunit->buildContainer($input->reveal());

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $container);

        $this->assertTrue($container->hasParameter('paraunit.max_process_count'), 'Max process count parameter missing');
        $this->assertEquals(10, $container->getParameter('paraunit.max_process_count'));

        $requiredDefinitions = array(
            'paraunit.file.cleaner',
            'paraunit.parser.json_log_parser',
            'paraunit.printer.process_printer',
            'paraunit.process.process_factory',
            'paraunit.runner.runner',
            'event_dispatcher',
            'paraunit.test_result.test_result_factory',
            'paraunit.test_result.pass_container',
            'paraunit.test_result.pass_test_result_format',
        );

        $servicesIds = $container->getServiceIds();
        $this->assertNotContains('paraunit.configuration.phpdbg_bin_file', $servicesIds);
        $this->assertNotContains('paraunit.coverage.coverage_fectcher', $servicesIds);

        foreach ($requiredDefinitions as $definition) {
            $this->assertContains($definition, $servicesIds);
            $container->get($definition); // test instantiation, to prevent misconfigurations
        }

        $this->markTestIncomplete('Awaiting #29 -- paraunit.printer.debug_printer');
    }
}
