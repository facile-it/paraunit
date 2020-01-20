<?php

declare(strict_types=1);

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultFactory;
use Paraunit\TestResult\TestResultFormat;
use Paraunit\TestResult\TestResultList;
use Paraunit\TestResult\TestResultWithSymbolFormat;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TestResultDefinition
{
    public function configure(ContainerBuilder $container): void
    {
        $container->setDefinition(TestResultFactory::class, new Definition(TestResultFactory::class));
        $this->configureTestResultContainer($container);
    }

    private function configureTestResultContainer(ContainerBuilder $container): void
    {
        $testResultList = new Definition(TestResultList::class);

        foreach ($this->getFormatDefinitions() as $name => $format) {
            $formatName = sprintf('paraunit.test_result.%s_format', $name);
            $testResultContainerName = sprintf('paraunit.test_result.%s_container', $name);

            $container->setDefinition($formatName, $format);
            $container->setDefinition($testResultContainerName, new Definition(TestResultContainer::class, [
                new Reference($formatName),
                new Reference(PHPUnitConfig::class),
            ]));

            $testResultList->addMethodCall('addContainer', [new Reference($testResultContainerName)]);
        }

        $container->setDefinition(TestResultList::class, $testResultList);
    }

    /**
     * @return Definition[]
     */
    private function getFormatDefinitions(): array
    {
        return [
            'unknown' => new Definition(TestResultWithSymbolFormat::class, [
                '?',
                'unknown',
                'unknown results (log parsing failed)',
            ]),
            'abnormal_terminated' => new Definition(TestResultWithSymbolFormat::class, [
                'X',
                'abnormal',
                'abnormal terminations (fatal errors, segfaults)',
            ]),
            'coverage_failure' => new Definition(TestResultFormat::class, [
                'error',
                'coverage not fetched',
            ]),
            'error' => new Definition(TestResultWithSymbolFormat::class, [
                'E',
                'error',
                'errors',
            ]),
            'failure' => new Definition(TestResultWithSymbolFormat::class, [
                'F',
                'fail',
                'failures',
            ]),
            'warning' => new Definition(TestResultWithSymbolFormat::class, [
                'W',
                'warning',
                'warnings',
            ]),
            'deprecation' => new Definition(TestResultFormat::class, [
                'fail',
                'deprecation warnings',
            ]),
            'no_test_executed' => new Definition(TestResultFormat::class, [
                'warning',
                'no tests executed',
            ]),
            'risky' => new Definition(TestResultWithSymbolFormat::class, [
                'R',
                'warning',
                'risky outcome',
            ]),
            'skipped' => new Definition(TestResultWithSymbolFormat::class, [
                'S',
                'skip',
                'skipped outcome',
                false,
            ]),
            'incomplete' => new Definition(TestResultWithSymbolFormat::class, [
                'I',
                'incomplete',
                'incomplete outcome',
                false,
            ]),
            'retry' => new Definition(TestResultWithSymbolFormat::class, [
                'A',
                'ok',
                'RETRIED',
                false,
            ]),
            'pass' => new Definition(TestResultWithSymbolFormat::class, [
                '.',
                'ok',
                'PASSED',
                false,
                false,
            ]),
        ];
    }
}
