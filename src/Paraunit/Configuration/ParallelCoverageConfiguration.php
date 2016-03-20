<?php

namespace Paraunit\Configuration;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ParallelCoverageConfiguration
 * @package Paraunit\Configuration
 */
class ParallelCoverageConfiguration extends ParallelConfiguration
{
    protected function loadYamlConfiguration(ContainerBuilder $containerBuilder)
    {
        $yamlLoader = parent::loadYamlConfiguration($containerBuilder);
        $yamlLoader->load('coverage.yml');
        $yamlLoader->load('coverage_configuration.yml');
        $yamlLoader->load('coverage_process.yml');
        $yamlLoader->load('coverage_proxy.yml');
    }
}
