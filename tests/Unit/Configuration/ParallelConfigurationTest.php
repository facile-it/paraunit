<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\ParallelConfiguration;
use Prophecy\Argument;
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
        $input = $this->prophesize('Symfony\Component\Console\Input\InputInterface');
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

        $container = $paraunit->buildContainer($input->reveal());

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $container);

        $requiredParameters = array(
            'paraunit.max_process_count' => 10,
            'paraunit.testsuite' => 'testsuite',
            'paraunit.string_filter' => 'text',
            'paraunit.phpunit_config_filename' => $this->getConfigForStubs(),
        );
        
        foreach ($requiredParameters as $parameterName => $expectedValue) {
            $this->assertTrue($container->hasParameter($parameterName), 'Parameter missing: ' . $parameterName);
            $this->assertEquals($expectedValue, $container->getParameter($parameterName));
        }

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
            'paraunit.configuration.phpunit_config',
        );

        $servicesIds = $container->getServiceIds();
        $this->assertNotContains('paraunit.configuration.phpdbg_bin_file', $servicesIds);
        $this->assertNotContains('paraunit.coverage.coverage_fetcher', $servicesIds);
        $this->assertNotContains('paraunit.printer.coverage_printer', $servicesIds);

        foreach ($requiredDefinitions as $definition) {
            $this->assertContains($definition, $servicesIds);
            $container->get($definition); // test instantiation, to prevent misconfigurations
        }

        $this->markTestIncomplete('Awaiting #29 -- paraunit.printer.debug_printer');
    }
}
