<?php

declare(strict_types=1);

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\File\Cleaner;
use Paraunit\File\TempDirectory;
use Paraunit\Filter\Filter;
use Paraunit\Printer\ConsoleFormatter;
use Paraunit\Printer\FailuresPrinter;
use Paraunit\Printer\FilesRecapPrinter;
use Paraunit\Printer\FinalPrinter;
use Paraunit\Printer\ProgressPrinter;
use Paraunit\Printer\SharkPrinter;
use Paraunit\Process\CommandLine;
use Paraunit\Process\ProcessFactory;
use Paraunit\Process\ProcessFactoryInterface;
use Paraunit\Runner\ChunkFile;
use Paraunit\Runner\PipelineCollection;
use Paraunit\Runner\PipelineFactory;
use Paraunit\Runner\Runner;
use Paraunit\TestResult\TestIssueContainer;
use Paraunit\TestResult\TestOutcomeContainer;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use SebastianBergmann\FileIterator\Facade;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class ParallelContainerDefinition
{
    private readonly ParserDefinition $parserDefinition;

    public function __construct()
    {
        $this->parserDefinition = new ParserDefinition();
    }

    public function configure(ContainerBuilder $container): ContainerBuilder
    {
        $container->setParameter('paraunit.max_retry_count', 3);
        $container->setParameter('kernel.root_dir', 'src');
        $this->configureConfiguration($container);
        $this->configureEventDispatcher($container);
        $this->configureFile($container);
        $this->parserDefinition->configure($container);
        $this->configurePrinter($container);
        $this->configureProcess($container);
        $this->configureRunner($container);
        $this->configureServices($container);

        return $container;
    }

    private function configureConfiguration(ContainerBuilder $container): void
    {
        $container->setDefinition(PHPUnitBinFile::class, new Definition(PHPUnitBinFile::class));
        $container->setDefinition(PHPUnitConfig::class, new Definition(PHPUnitConfig::class, [
            '%paraunit.phpunit_config_filename%',
        ]))
            ->setPublic(true);
        $container->setDefinition(TempFilenameFactory::class, new Definition(TempFilenameFactory::class, [
            new Reference(TempDirectory::class),
        ]));
        $container->setDefinition(ChunkSize::class, new Definition(ChunkSize::class, [
            '%paraunit.chunk_size%',
        ]));
    }

    private function configureEventDispatcher(ContainerBuilder $container): void
    {
        $dispatcher = new Definition(EventDispatcher::class);
        $container->setDefinition(SymfonyEventDispatcherInterface::class, $dispatcher);
        $container->setAlias('event_dispatcher', SymfonyEventDispatcherInterface::class);
        $container->setAlias(PsrEventDispatcherInterface::class, SymfonyEventDispatcherInterface::class);

        $container->addCompilerPass(new RegisterListenersPass());
    }

    private function configureFile(ContainerBuilder $container): void
    {
        $container->setDefinition(TempDirectory::class, new Definition(TempDirectory::class));
        $container->setDefinition(Cleaner::class, new Definition(Cleaner::class, [
            new Reference(TempDirectory::class),
        ]));
    }

    private function configurePrinter(ContainerBuilder $container): void
    {
        $container->autowire(TestOutcomeContainer::class);
        $container->autowire(TestIssueContainer::class);

        $container->setDefinition(SharkPrinter::class, new Definition(SharkPrinter::class, [
            new Reference(OutputInterface::class),
            '%paraunit.show_logo%',
        ]));
        $container->setDefinition(ProgressPrinter::class, new Definition(ProgressPrinter::class, [
            new Reference(OutputInterface::class),
        ]));

        $container->setDefinition(FinalPrinter::class, new Definition(FinalPrinter::class, [
            new Reference(OutputInterface::class),
            new Reference(ChunkSize::class),
        ]));

        $container->setDefinition(FailuresPrinter::class, new Definition(FailuresPrinter::class, [
            new Reference(OutputInterface::class),
            new Reference(TestOutcomeContainer::class),
        ]));

        $container->setDefinition(FilesRecapPrinter::class, new Definition(FilesRecapPrinter::class, [
            new Reference(OutputInterface::class),
            new Reference(TestOutcomeContainer::class),
            new Reference(ChunkSize::class),
        ]));

        $container->setDefinition(ConsoleFormatter::class, new Definition(ConsoleFormatter::class, [
            (new Definition(OutputFormatterInterface::class))->setFactory([new Reference(OutputInterface::class), 'getFormatter']),
        ]));
    }

    private function configureProcess(ContainerBuilder $container): void
    {
        $container->setDefinition(CommandLine::class, new Definition(CommandLine::class, [
            new Reference(PHPUnitBinFile::class),
            new Reference(ChunkSize::class),
        ]));

        $container->setDefinition(ProcessFactoryInterface::class, new Definition(ProcessFactory::class, [
            new Reference(CommandLine::class),
            new Reference(PHPUnitConfig::class),
            new Reference(TempFilenameFactory::class),
            new Reference(ChunkSize::class),
        ]));
    }

    private function configureRunner(ContainerBuilder $container): void
    {
        $container->setDefinition(PipelineFactory::class, new Definition(PipelineFactory::class, [
            new Reference(PsrEventDispatcherInterface::class),
        ]));
        $container->setDefinition(PipelineCollection::class, new Definition(PipelineCollection::class, [
            new Reference(PipelineFactory::class),
            '%paraunit.max_process_count%',
        ]));
        $container->setDefinition(Runner::class, new Definition(Runner::class, [
            new Reference(PsrEventDispatcherInterface::class),
            new Reference(ProcessFactoryInterface::class),
            new Reference(Filter::class),
            new Reference(PipelineCollection::class),
            new Reference(ChunkSize::class),
            new Reference(ChunkFile::class),
        ]))
            ->setPublic(true);

        $container->setDefinition(ChunkFile::class, new Definition(ChunkFile::class, [
            new Reference(PHPUnitConfig::class),
        ]));
    }

    private function configureServices(ContainerBuilder $container): void
    {
        $container->register(OutputInterface::class, OutputInterface::class)
            ->setPublic(true)
            ->setSynthetic(true);
        $container->setDefinition(Facade::class, new Definition(Facade::class));
        $container->setDefinition(Filter::class, new Definition(Filter::class, [
            new Reference(Facade::class),
            new Reference(PHPUnitConfig::class),
            '%paraunit.testsuite%',
            '%paraunit.string_filter%',
        ]));
    }
}
