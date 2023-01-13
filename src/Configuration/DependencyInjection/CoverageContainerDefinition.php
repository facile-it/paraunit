<?php

declare(strict_types=1);

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\Coverage\CoverageMerger;
use Paraunit\Coverage\CoverageResult;
use Paraunit\Printer\CoveragePrinter;
use Paraunit\Process\CommandLineWithCoverage;
use Paraunit\Process\ProcessFactoryInterface;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
        $container->setDefinition(PHPDbgBinFile::class, new Definition(PHPDbgBinFile::class));
        $container->setDefinition(XDebugProxy::class, new Definition(XDebugProxy::class));
        $container->setDefinition(PcovProxy::class, new Definition(PcovProxy::class));
    }

    private function configureProcessWithCoverage(ContainerBuilder $container): void
    {
        $container->setDefinition(CommandLineWithCoverage::class, new Definition(CommandLineWithCoverage::class, [
            new Reference(PHPUnitBinFile::class),
            new Reference(ChunkSize::class),
            new Reference(PcovProxy::class),
            new Reference(XDebugProxy::class),
            new Reference(PHPDbgBinFile::class),
            new Reference(TempFilenameFactory::class),
        ]));

        $container->getDefinition(ProcessFactoryInterface::class)
            ->setArguments([
                new Reference(CommandLineWithCoverage::class),
                new Reference(PHPUnitConfig::class),
                new Reference(TempFilenameFactory::class),
                new Reference(ChunkSize::class),
            ]);
    }

    private function configureCoverage(ContainerBuilder $container): void
    {
        $container->setDefinition(CoverageFetcher::class, new Definition(CoverageFetcher::class, [
            new Reference(TempFilenameFactory::class),
            new Reference('paraunit.test_result.coverage_failure_container'),
        ]));
        $container->setDefinition(CoverageMerger::class, new Definition(CoverageMerger::class, [
            new Reference(CoverageFetcher::class),
        ]));
        $container->setDefinition(CoverageResult::class, new Definition(CoverageResult::class, [
            new Reference(CoverageMerger::class),
        ]));
        $container->setDefinition(CoveragePrinter::class, new Definition(CoveragePrinter::class, [
            new Reference(CommandLineWithCoverage::class),
            new Reference(OutputInterface::class),
        ]));
    }
}
