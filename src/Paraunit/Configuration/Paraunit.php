<?php

namespace Paraunit\Configuration;

use Paraunit\Parser\ParserCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * Class Paraunit
 * @package Paraunit\Configuration
 */
class Paraunit
{
    const PARAUNIT_VERSION = '0.6.2';

    /**
     * @return ContainerBuilder
     */
    public static function buildContainer()
    {
        $containerBuilder = new ContainerBuilder();

        $xmlLoader = new XmlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config/'));
        $xmlLoader->load('config.xml');

        $yamlLoader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config/'));
        $yamlLoader->load('configuration.yml');
        $yamlLoader->load('file.yml');
        $yamlLoader->load('parser.yml');
        $yamlLoader->load('printer.yml');
        $yamlLoader->load('services.yml');
        $yamlLoader->load('test_result.yml');
        $yamlLoader->load('test_result_container.yml');
        $yamlLoader->load('test_result_format.yml');

        $containerBuilder->addCompilerPass(new RegisterListenersPass());
        $containerBuilder->addCompilerPass(new ParserCompilerPass());

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
