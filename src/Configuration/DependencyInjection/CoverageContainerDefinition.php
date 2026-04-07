<?php

declare(strict_types=1);

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\Coverage\CoverageMerger;
use Paraunit\Coverage\CoverageResult;
use Paraunit\Coverage\V14;
use Paraunit\Printer\CoveragePrinter;
use Paraunit\Process\CommandLineWithCoverage;
use Paraunit\Process\ProcessFactory;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;
use SebastianBergmann\CodeCoverage\Node\Directory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CoverageContainerDefinition extends ParallelContainerDefinition
{
    public function configure(ContainerBuilder $container): ContainerBuilder
    {
        parent::configure($container);

        $this->configureCoverageConfiguration($container);
        $this->configureProcessWithCoverage($container);
        $this->configureCoverage($container);

        return $container;
    }

    private function configureCoverageConfiguration(ContainerBuilder $container): void
    {
        $container->autowire(PHPDbgBinFile::class);
        $container->autowire(XDebugProxy::class);
        $container->autowire(PcovProxy::class);
    }

    private function configureProcessWithCoverage(ContainerBuilder $container): void
    {
        $container->autowire(CommandLineWithCoverage::class);

        $container->autowire(ProcessFactory::class)
            ->setArgument('$cliCommand', new Reference(CommandLineWithCoverage::class));
    }

    private function configureCoverage(ContainerBuilder $container): void
    {
        $container->autowire(CoveragePrinter::class);

        if (class_exists(Directory::class)) {
            // PHPUnit 13.1+ with phpunit/php-code-coverage v14
            $container->autowire(V14\CoverageFetcher::class);
            $container->autowire(V14\CoverageMerger::class);
            $container->autowire(V14\CoverageResult::class);
        } else {
            // PHPUnit 13.0 with phpunit/php-code-coverage v13 and below
            $container->autowire(CoverageFetcher::class);
            $container->autowire(CoverageMerger::class);
            $container->autowire(CoverageResult::class);
        }
    }
}
