<?php

namespace Paraunit\Configuration;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
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
     * @param InputInterface $input
     * @return ContainerBuilder
     * @throws \Exception
     */
    public function buildContainer(InputInterface $input)
    {
        $containerBuilder = new ContainerBuilder();

        $this->loadYamlConfiguration($containerBuilder);
        $this->registerEventDispatcher($containerBuilder);
        $this->loadCommandLineOptions($containerBuilder, $input);

        $containerBuilder->compile();

        $this->loadPostCompileSettings($containerBuilder, $input);

        return $containerBuilder;
    }

    /**
     * @param ContainerBuilder $containerBuilder
     * @return YamlFileLoader
     * @throws \Exception
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

        $eventDispatcherPass = 'Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass';
        $deprecatedPass = 'Symfony\Component\HttpKernel\DependencyInjection\RegisterListenersPass';
        if (! class_exists($eventDispatcherPass)) {
            class_alias($deprecatedPass, $eventDispatcherPass);
        }

        return $loader;
    }

    /**
     * @param ContainerBuilder $containerBuilder
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    private function registerEventDispatcher(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new RegisterListenersPass());

        $containerBuilder->setDefinition(
            'event_dispatcher',
            new Definition(
                'Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher',
                array(new Reference('service_container'))
            )
        );
    }

    protected function loadCommandLineOptions(ContainerBuilder $containerBuilder, InputInterface $input)
    {
        $containerBuilder->setParameter('paraunit.max_process_count', $input->getOption('parallel'));
        $containerBuilder->setParameter('paraunit.phpunit_config_filename', $input->getOption('configuration'));
    }

    protected function loadPostCompileSettings(ContainerBuilder $container, InputInterface $input)
    {
    }
}
