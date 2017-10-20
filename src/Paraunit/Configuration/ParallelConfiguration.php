<?php
declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Configuration\DependencyInjection\ParallelContainerDefinition;
use Paraunit\Printer\DebugPrinter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Paraunit
 * @package Paraunit\Configuration
 */
class ParallelConfiguration
{
    const TAG_EVENT_SUBSCRIBER = 'paraunit.event_subscriber';

    /** @var ParallelContainerDefinition */
    protected $containerDefinition;

    public function __construct()
    {
        $this->containerDefinition = new ParallelContainerDefinition();
    }

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
        $this->containerDefinition->configure($containerBuilder);
        $this->loadCommandLineOptions($containerBuilder, $input);
        $this->tagEventSubscribers($containerBuilder);

        $containerBuilder->compile();

        $this->loadPostCompileSettings($containerBuilder, $input);

        return $containerBuilder;
    }

    protected function tagEventSubscribers(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->isSynthetic()) {
                continue;
            }

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
        $containerBuilder->setParameter('paraunit.show_logo', $input->getOption('logo'));

        if ($input->getOption('debug')) {
            $this->enableDebugMode($containerBuilder);
        }
    }

    protected function loadPostCompileSettings(ContainerBuilder $container, InputInterface $input)
    {
    }

    private function injectOutput(ContainerBuilder $containerBuilder, OutputInterface $output)
    {
        $containerBuilder->register(OutputInterface::class)
            ->setSynthetic(true);

        $containerBuilder->set(OutputInterface::class, $output);
    }

    private function enableDebugMode(ContainerBuilder $containerBuilder)
    {
        $definition = new Definition(DebugPrinter::class, [new Reference(OutputInterface::class)]);
        $definition->addTag(self::TAG_EVENT_SUBSCRIBER);

        $containerBuilder->setDefinition(DebugPrinter::class, $definition);
    }
}
