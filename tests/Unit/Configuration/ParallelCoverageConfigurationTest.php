<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\ParallelCoverageConfiguration;

/**
 * Class ParaunitCoverageTest
 * @package Tests\Unit\Configuration
 */
class ParallelCoverageConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildContainer()
    {
        $paraunit = new ParallelCoverageConfiguration();

        $container = $paraunit->buildContainer();

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $container);

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
        $this->markTestIncomplete();
        $this->assertContains('paraunit.printer.debug_printer', $servicesIds);
        $this->assertContains('paraunit.coverage.coverage_output_paths', $servicesIds);
    }
}
