<?php

declare(strict_types=1);

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Logs\JSON\LogFetcher;
use Paraunit\Logs\JSON\LogHandler;
use Paraunit\Logs\JSON\LogParser;
use Paraunit\Logs\JSON\RetryParser;
use Paraunit\TestResult\TestResultContainer;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ParserDefinition
{
    public function configure(ContainerBuilder $container): void
    {
        $container->setDefinition(LogHandler::class, new Definition(LogHandler::class, [
            new Reference(TestResultContainer::class),
        ]));

        $container->setDefinition(LogParser::class, new Definition(LogParser::class, [
            new Reference(LogFetcher::class),
            new Reference(LogHandler::class),
            new Reference(RetryParser::class),
            new Reference(EventDispatcherInterface::class),
        ]));

        $container->setDefinition(RetryParser::class, new Definition(RetryParser::class, [
            new Reference(TestResultContainer::class),
            '%paraunit.max_retry_count%',
        ]));

        $container->setDefinition(LogFetcher::class, new Definition(LogFetcher::class, [
            new Reference(TempFilenameFactory::class),
        ]));
    }
}
