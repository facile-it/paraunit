<?php

declare(strict_types=1);

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Logs\JSON\LogFetcher;
use Paraunit\Logs\JSON\LogHandler;
use Paraunit\Logs\JSON\LogParser;
use Paraunit\Logs\JSON\RetryParser;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ParserDefinition
{
    public function configure(ContainerBuilder $container): void
    {
        $container->autowire(LogHandler::class);

        $container->autowire(LogParser::class);

        $container->autowire(RetryParser::class)
            ->setArgument('$maxRetryCount', '%paraunit.max_retry_count%');

        $container->autowire(LogFetcher::class);
    }
}
