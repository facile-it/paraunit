<?php

namespace Paraunit\Configuration;

use Paraunit\Parser\ParserCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * Class Paraunit
 * @package Paraunit\Configuration
 */
class ParallelConfiguration
{
    /**
     * @return ContainerBuilder
     */
    public function buildContainer()
    {
        $containerBuilder = new ContainerBuilder();

        $this->loadYamlConfiguration($containerBuilder);
        $this->registerEventDispatcher($containerBuilder);
        $containerBuilder->compile();

        return $containerBuilder;
    }

    /**
     * @param ContainerBuilder $containerBuilder
     * @return YamlFileLoader
     */
    protected function loadYamlConfiguration(ContainerBuilder $containerBuilder)
    {
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config/'));
        $loader->load('configuration.yml');
        $loader->load('file.yml');
        $loader->load('parser.yml');
        $loader->load('printer.yml');
        $loader->load('process.yml');
        $loader->load('services.yml');
        $loader->load('test_result.yml');
        $loader->load('test_result_container.yml');
        $loader->load('test_result_format.yml');

        return $loader;
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    private function registerEventDispatcher(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterListenersPass());
        $containerBuilder->addCompilerPass(new ParserCompilerPass());

        $containerBuilder->setDefinition(
            'event_dispatcher',
            new Definition(
                'Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher',
                array(new Reference('service_container'))
            )
        );
    }
}
