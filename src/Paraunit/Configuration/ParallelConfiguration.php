<?php
declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Printer\DebugPrinter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Paraunit
 * @package Paraunit\Configuration
 */
class ParallelConfiguration
{
    const TAG_EVENT_SUBSCRIBER = 'paraunit.event_subscriber';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ContainerBuilder
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \Exception
     */
    public function buildContainer(InputInterface $input, OutputInterface $output): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $this->injectOutput($containerBuilder, $output);
        $this->loadYamlConfiguration($containerBuilder);
        $this->loadCommandLineOptions($containerBuilder, $input);
        $this->tagEventSubscribers($containerBuilder);

        if ($input->getOption('debug')) {
            $this->enableDebugMode($containerBuilder);
        }

        $containerBuilder->compile();

        $this->loadPostCompileSettings($containerBuilder, $input);

        return $containerBuilder;
    }

    /**
     * @param ContainerBuilder $containerBuilder
     * @return YamlFileLoader
     * @throws \Exception
     */
    protected function loadYamlConfiguration(ContainerBuilder $containerBuilder): YamlFileLoader
    {
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config/'));
        $loader->load('configuration.yml');
        $loader->load('file.yml');
        $loader->load('parser.yml');
        $loader->load('printer.yml');
        $loader->load('process.yml');
        $loader->load('runner.yml');
        $loader->load('services.yml');
        $loader->load('test_result.yml');
        $loader->load('test_result_container.yml');
        $loader->load('test_result_format.yml');

        return $loader;
    }

    protected function tagEventSubscribers(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterListenersPass('event_dispatcher', null, self::TAG_EVENT_SUBSCRIBER));

        foreach ($container->getDefinitions() as $definition) {
            $reflection = new \ReflectionClass($definition->getClass());

            if ($reflection->implementsInterface(EventSubscriberInterface::class)) {
                $definition->addTag(self::TAG_EVENT_SUBSCRIBER);
            }
        }
    }

    protected function loadCommandLineOptions(ContainerBuilder $containerBuilder, InputInterface $input)
    {
        $containerBuilder->setParameter('paraunit.max_process_count', $input->getOption('parallel'));
        $containerBuilder->setParameter('paraunit.phpunit_config_filename', $input->getOption('configuration') ?? '.');
        $containerBuilder->setParameter('paraunit.testsuite', $input->getOption('testsuite'));
        $containerBuilder->setParameter('paraunit.string_filter', $input->getArgument('stringFilter'));
    }

    protected function loadPostCompileSettings(ContainerBuilder $container, InputInterface $input)
    {
    }

    private function injectOutput(ContainerBuilder $containerBuilder, OutputInterface $output)
    {
        $containerBuilder->register('output')
            ->setSynthetic(true);

        $containerBuilder->set('output', $output);
    }

    private function enableDebugMode(ContainerBuilder $containerBuilder)
    {
        $definition = new Definition(DebugPrinter::class, [new Reference('output')]);
        $definition->setPublic(false);
        $definition->addTag(self::TAG_EVENT_SUBSCRIBER);

        $containerBuilder->setDefinition('paraunit.printer.debug_printer', $definition);
    }
}
