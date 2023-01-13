<?php

declare(strict_types=1);

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\DeprecationParser;
use Paraunit\Parser\JSON\AbnormalTerminatedParser;
use Paraunit\Parser\JSON\GenericParser;
use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\Parser\JSON\LogParser;
use Paraunit\Parser\JSON\RetryParser;
use Paraunit\Parser\JSON\UnknownResultParser;
use Paraunit\Parser\ValueObject\TestStatus;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ParserDefinition
{
    public function configure(ContainerBuilder $container): void
    {
        $logParser = new Definition(LogParser::class, [
            new Reference(LogFetcher::class),
            new Reference('paraunit.test_result.no_test_executed_container'),
            new Reference(EventDispatcherInterface::class),
            new Reference(RetryParser::class),
        ]);

        foreach ($this->defineParsers($container) as $reference) {
            $logParser->addMethodCall('addParser', [$reference]);
        }

        $container->setDefinition(RetryParser::class, new Definition(RetryParser::class, [
            new Reference('paraunit.test_result.retry_container'),
            '%paraunit.max_retry_count%',
        ]));

        $container->setDefinition(LogParser::class, $logParser);
        $container->setDefinition(LogFetcher::class, new Definition(LogFetcher::class, [
            new Reference(TempFilenameFactory::class),
        ]));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     *
     * @return Reference[]
     */
    private function defineParsers(ContainerBuilder $container): array
    {
        $parserDefinitions = [
            'paraunit.parser.success_parser' => new Definition(GenericParser::class, [
                new Reference('paraunit.test_result.success_container'),
                TestStatus::Passed,
            ]),
            'paraunit.parser.incomplete_parser' => new Definition(GenericParser::class, [
                new Reference('paraunit.test_result.incomplete_container'),
                TestStatus::MarkedIncomplete,
            ]),
            'paraunit.parser.skipped_parser' => new Definition(GenericParser::class, [
                new Reference('paraunit.test_result.skipped_container'),
                TestStatus::Skipped,
            ]),
            'paraunit.parser.risky_parser' => new Definition(GenericParser::class, [
                new Reference('paraunit.test_result.risky_container'),
                TestStatus::ConsideredRisky,
            ]),
            'paraunit.parser.warning_parser' => new Definition(GenericParser::class, [
                new Reference('paraunit.test_result.warning_container'),
                TestStatus::WarningTriggered,
            ]),
            'paraunit.parser.failure_parser' => new Definition(GenericParser::class, [
                new Reference('paraunit.test_result.failure_container'),
                TestStatus::Failed,
            ]),
            'paraunit.parser.error_parser' => new Definition(GenericParser::class, [
                new Reference('paraunit.test_result.error_container'),
                TestStatus::Errored,
            ]),
            AbnormalTerminatedParser::class => new Definition(AbnormalTerminatedParser::class, [
                new Reference('paraunit.test_result.abnormal_terminated_container'),
                new Reference(ChunkSize::class),
                TestStatus::LogTerminated,
            ]),
            UnknownResultParser::class => new Definition(UnknownResultParser::class, [
                new Reference('paraunit.test_result.unknown_container'),
            ]),
        ];

        $parserReferences = [];
        foreach ($parserDefinitions as $name => $definition) {
            $container->setDefinition($name, $definition);
            $parserReferences[] = new Reference($name);
        }

        $container->setDefinition(DeprecationParser::class, new Definition(DeprecationParser::class, [
            new Reference('paraunit.test_result.deprecation_container'),
        ]));

        return $parserReferences;
    }
}
