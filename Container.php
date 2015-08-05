<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

function getContainer(){

    $container = new ContainerBuilder();

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/src/Paraunit/Resources/config/'));
    $loader->load('services.yml');

    $container->addCompilerPass(
        new \Paraunit\Lifecycle\CompilerPass(
            'event_dispatcher',
            'paraunit.event_listener',
            'paraunit.event_subscriber'
        )
    );

    $container->compile();

    return $container;
}

$container = getContainer();