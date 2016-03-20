<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\ParallelConfiguration;

/**
 * Class ParaunitTest
 * @package Tests\Unit\Configuration
 */
class ParallelConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildContainer()
    {
        $paraunit = new ParallelConfiguration();

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

        $this->assertNotContains('paraunit.coverage.coverage_fectcher', $servicesIds);

        $this->markTestIncomplete();
        $this->assertContains('paraunit.printer.debug_printer', $servicesIds);
    }
}
