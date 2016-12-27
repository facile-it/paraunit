<?php

namespace Paraunit\Configuration;

use Symfony\Component\Console\Input\InputInterface;
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

    protected function loadCommandLineOptions(ContainerBuilder $containerBuilder, InputInterface $input)
    {
        parent::loadCommandLineOptions($containerBuilder, $input);

        $containerBuilder->setParameter('paraunit.coverage.clover_file_path', $input->getOption('clover'));
        $containerBuilder->setParameter('paraunit.coverage.xml_file_path', $input->getOption('xml'));
        $containerBuilder->setParameter('paraunit.coverage.html_path', $input->getOption('html'));
        $containerBuilder->setParameter('paraunit.coverage.text_path', $input->getOption('text'));
        $containerBuilder->setParameter('paraunit.coverage.text_to_console', $input->getOption('text-to-console'));
    }
}
