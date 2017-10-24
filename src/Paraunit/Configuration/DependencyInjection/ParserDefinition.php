<?php

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\DeprecationParser;
use Paraunit\Parser\JSON\AbnormalTerminatedParser;
use Paraunit\Parser\JSON\GenericParser;
use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\Parser\JSON\LogParser;
use Paraunit\Parser\JSON\LogPrinter;
use Paraunit\Parser\JSON\RetryParser;
use Paraunit\Parser\JSON\TestStartParser;
use Paraunit\Parser\JSON\UnknownResultParser;
use Paraunit\TestResult\TestResultFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ParserDefinition
{
    public function configure(ContainerBuilder $container)
    {
        $logParser = new Definition(LogParser::class, [
            new Reference(LogFetcher::class),
            new Reference('paraunit.test_result.no_test_executed_container'),
            new Reference(EventDispatcherInterface::class),
        ]);

        foreach ($this->defineParsers($container) as $reference) {
            $logParser->addMethodCall('addParser', [$reference]);
        }

        $container->setDefinition(LogParser::class, $logParser);
        $container->setDefinition(LogFetcher::class, new Definition(LogFetcher::class, [
            new Reference(TempFilenameFactory::class),
        ]));
    }

    /**
     * @param ContainerBuilder $container
     * @return Reference[]
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    private function defineParsers(ContainerBuilder $container): array
    {
        $testResultFactory = new Reference(TestResultFactory::class);
        $parserDefinitions = [
            TestStartParser::class => new Definition(TestStartParser::class),
            'paraunit.parser.pass_parser' => new Definition(GenericParser::class, [
                $testResultFactory,
                new Reference('paraunit.test_result.pass_container'),
                LogPrinter::STATUS_PASS,
            ]),
            RetryParser::class => new Definition(RetryParser::class, [
                new Reference('paraunit.test_result.retry_container'),
                '%paraunit.max_retry_count%',
            ]),
            'paraunit.parser.incomplete_parser' => new Definition(GenericParser::class, [
                $testResultFactory,
                new Reference('paraunit.test_result.incomplete_container'),
                LogPrinter::STATUS_ERROR,
                LogPrinter::MESSAGE_INCOMPLETE_TEST,
            ]),
            'paraunit.parser.skipped_parser' => new Definition(GenericParser::class, [
                $testResultFactory,
                new Reference('paraunit.test_result.skipped_container'),
                LogPrinter::STATUS_ERROR,
                LogPrinter::MESSAGE_SKIPPED_TEST,
            ]),
            'paraunit.parser.risky_parser' => new Definition(GenericParser::class, [
                $testResultFactory,
                new Reference('paraunit.test_result.risky_container'),
                LogPrinter::STATUS_ERROR,
                LogPrinter::MESSAGE_RISKY_TEST,
            ]),
            'paraunit.parser.warning_parser' => new Definition(GenericParser::class, [
                $testResultFactory,
                new Reference('paraunit.test_result.warning_container'),
                LogPrinter::STATUS_WARNING,
            ]),
            'paraunit.parser.failure_parser' => new Definition(GenericParser::class, [
                $testResultFactory,
                new Reference('paraunit.test_result.failure_container'),
                LogPrinter::STATUS_FAIL,
            ]),
            'paraunit.parser.error_parser' => new Definition(GenericParser::class, [
                $testResultFactory,
                new Reference('paraunit.test_result.error_container'),
                LogPrinter::STATUS_ERROR,
            ]),
            AbnormalTerminatedParser::class => new Definition(AbnormalTerminatedParser::class, [
                $testResultFactory,
                new Reference('paraunit.test_result.abnormal_terminated_container'),
                LogFetcher::LOG_ENDING_STATUS,
            ]),
            UnknownResultParser::class => new Definition(UnknownResultParser::class, [
                $testResultFactory,
                new Reference('paraunit.test_result.unknown_container'),
                '',
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
