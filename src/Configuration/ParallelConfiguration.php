<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Configuration\DependencyInjection\ParallelContainerDefinition;
use Paraunit\Printer\DebugPrinter;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ParallelConfiguration
{
    public const TAG_EVENT_SUBSCRIBER = 'paraunit.event_subscriber';

    public const PUBLIC_ALIAS_FORMAT = '%s_public_alias';

    /** @var ParallelContainerDefinition */
    protected $containerDefinition;

    /** @var bool */
    private $createPublicServiceAliases;

    public function __construct(bool $createPublicServiceAliases = false)
    {
        $this->containerDefinition = new ParallelContainerDefinition();
        $this->createPublicServiceAliases = $createPublicServiceAliases;
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \Exception
     */
    public function buildContainer(InputInterface $input, OutputInterface $output): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $this->containerDefinition->configure($containerBuilder);
        $this->loadCommandLineOptions($containerBuilder, $input);
        $this->tagEventSubscribers($containerBuilder);

        $this->createPublicAliases($containerBuilder);
        $containerBuilder->compile();
        $containerBuilder->set(OutputInterface::class, $output);

        return $containerBuilder;
    }

    protected function tagEventSubscribers(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition->isSynthetic() || $definition->isAbstract()) {
                continue;
            }

            $class = $definition->getClass() ?? (string) $id;
            $implements = class_implements($class);
            if ($implements && array_key_exists(EventSubscriberInterface::class, $implements)) {
                $definition->addTag(self::TAG_EVENT_SUBSCRIBER);
            }
        }
    }

    protected function loadCommandLineOptions(ContainerBuilder $containerBuilder, InputInterface $input): void
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

    private function enableDebugMode(ContainerBuilder $containerBuilder): void
    {
        $definition = new Definition(DebugPrinter::class, [new Reference(OutputInterface::class)]);
        $definition->addTag(self::TAG_EVENT_SUBSCRIBER);

        $containerBuilder->setDefinition(DebugPrinter::class, $definition);
    }

    private function createPublicAliases(ContainerBuilder $containerBuilder): void
    {
        if (! $this->createPublicServiceAliases) {
            return;
        }

        $services = $containerBuilder->getServiceIds();
        // the synthetic service isn't listed
        $services[] = OutputInterface::class;
        foreach ($services as $serviceName) {
            if ($serviceName === ContainerInterface::class || $serviceName === SymfonyContainerInterface::class) {
                // avoid deprecation
                continue;
            }

            $containerBuilder->setAlias(
                sprintf(self::PUBLIC_ALIAS_FORMAT, $serviceName),
                new Alias($serviceName, true)
            );
        }
    }
}
