<?php

namespace Paraunit\Configuration;

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
class Paraunit
{
    const PARAUNIT_VERSION = '0.5.1';

    /**
     * @return ContainerBuilder
     */
    public static function buildContainer()
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config/'));
        $loader->load('configuration.yml');
        $loader->load('file.yml');
        $loader->load('output_container.yml');
        $loader->load('parser.yml');
        $loader->load('printer.yml');
        $loader->load('services.yml');

        $containerBuilder->addCompilerPass(new RegisterListenersPass());

        $containerBuilder->setDefinition(
            'event_dispatcher',
            new Definition(
                'Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher',
                array(new Reference('service_container'))
            )
        );

        $containerBuilder->compile();

        return $containerBuilder;
    }
}
