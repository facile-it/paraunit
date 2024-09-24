<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Configuration\DependencyInjection\ParallelContainerDefinition;
use Paraunit\Filter\Filter;
use Paraunit\Filter\RandomizeList;
use Paraunit\Filter\TestList;
use Paraunit\Printer\DebugPrinter;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ParallelConfiguration
{
    final public const TAG_EVENT_SUBSCRIBER = 'kernel.event_subscriber';

    final public const PUBLIC_ALIAS_FORMAT = '%s_public_alias';

    protected ParallelContainerDefinition $containerDefinition;

    public function __construct(private readonly bool $createPublicServiceAliases = false)
    {
        $this->containerDefinition = new ParallelContainerDefinition();
    }

    /**
     * @throws BadMethodCallException
     * @throws \Exception
     */
    public function buildContainer(InputInterface $input, OutputInterface $output): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $this->containerDefinition->configure($containerBuilder);
        $this->loadCommandLineOptions($containerBuilder, $input);
        $this->tagEventSubscribers($containerBuilder);
        $this->postProcessConfiguration($containerBuilder);

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

            $class = $definition->getClass() ?? $id;
            $implements = class_implements($class);
            if (false !== $implements && array_key_exists(EventSubscriberInterface::class, $implements)) {
                $definition->addTag(self::TAG_EVENT_SUBSCRIBER);
            }
        }
    }

    protected function loadCommandLineOptions(ContainerBuilder $containerBuilder, InputInterface $input): void
    {
        $containerBuilder->setParameter('paraunit.max_process_count', $input->getOption('parallel'));
        $containerBuilder->setParameter('paraunit.chunk_size', $input->getOption('chunk-size'));
        $containerBuilder->setParameter('paraunit.phpunit_config_filename', $input->getOption('configuration') ?? '.');
        $containerBuilder->setParameter('paraunit.testsuite', $input->getOption('testsuite'));
        $containerBuilder->setParameter('paraunit.string_filter', $input->getArgument('stringFilter'));
        $containerBuilder->setParameter('paraunit.show_logo', $input->getOption('logo'));
        $containerBuilder->setParameter('paraunit.pass_through', $input->getOption('pass-through'));
        $containerBuilder->setParameter('paraunit.sort_order', $input->getOption('sort'));
        $containerBuilder->setParameter('paraunit.exclude_testsuite', $input->getOption('exclude-testsuite'));
        $containerBuilder->setParameter('paraunit.test_suffix', $input->getOption('test-suffix'));

        if ($input->getOption('debug')) {
            $this->enableDebugMode($containerBuilder);
        }
    }

    private function enableDebugMode(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->autowire(DebugPrinter::class)
            ->addTag(self::TAG_EVENT_SUBSCRIBER);
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

    private function postProcessConfiguration(ContainerBuilder $container): void
    {
        $sortOrder = $container->hasParameter('paraunit.sort_order')
            ? $container->getParameter('paraunit.sort_order')
            : null;

        if (is_string($sortOrder)) {
            if ($sortOrder !== 'random') {
                throw new \InvalidArgumentException('Unexpected value for --sort option: ' . $sortOrder);
            }

            $container->setDefinition(TestList::class, new Definition(RandomizeList::class, [
                new Reference(Filter::class),
            ]));
        } else {
            $container->setAlias(TestList::class, Filter::class);
        }
    }
}
