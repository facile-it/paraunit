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
use Paraunit\TestResult\TestResultContainer;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;
use SebastianBergmann\FileIterator\Facade;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        $container->autowire(PHPUnitBinFile::class);
        $container->autowire(PHPUnitConfig::class)
            ->setArgument('$inputPathOrFileName', '%paraunit.phpunit_config_filename%')
            ->setPublic(true);

        $container->autowire(TempFilenameFactory::class);
        $container->autowire(ChunkSize::class)
            ->setArgument('$chunkSize', '%paraunit.chunk_size%');
    }

    private function configureEventDispatcher(ContainerBuilder $container): void
    {
        $container->autowire(SymfonyEventDispatcherInterface::class, EventDispatcher::class);
        $container->setAlias('event_dispatcher', SymfonyEventDispatcherInterface::class);
        $container->setAlias(PsrEventDispatcherInterface::class, SymfonyEventDispatcherInterface::class);

        $container->addCompilerPass(new RegisterListenersPass());
    }

    private function configureFile(ContainerBuilder $container): void
    {
        $container->autowire(TempDirectory::class);
        $container->autowire(Cleaner::class);
    }

    private function configurePrinter(ContainerBuilder $container): void
    {
        $container->autowire(TestResultContainer::class);

        $container->autowire(SharkPrinter::class)
            ->setArgument('$showLogo', '%paraunit.show_logo%');

        $container->autowire(ProgressPrinter::class);
        $container->autowire(FinalPrinter::class);
        $container->autowire(FailuresPrinter::class);
        $container->autowire(FilesRecapPrinter::class);

        $container->autowire(ConsoleFormatter::class);
        $container->autowire(OutputFormatterInterface::class)
            ->setFactory([new Reference(OutputInterface::class), 'getFormatter']);
    }

    private function configureProcess(ContainerBuilder $container): void
    {
        $container->autowire(CommandLine::class);
        $container->autowire(ProcessFactoryInterface::class, ProcessFactory::class);
    }

    private function configureRunner(ContainerBuilder $container): void
    {
        $container->autowire(PipelineFactory::class);
        $container->autowire(PipelineCollection::class)
            ->setArgument('$maxProcessNumber', '%paraunit.max_process_count%');
        $container->autowire(Runner::class)
            ->setPublic(true);
        $container->autowire(ChunkFile::class);
    }

    private function configureServices(ContainerBuilder $container): void
    {
        $container->register(OutputInterface::class, OutputInterface::class)
            ->setPublic(true)
            ->setSynthetic(true);

        $container->autowire(Facade::class);
        $container->autowire(Filter::class)
            ->setArgument('$testSuiteFilter', '%paraunit.testsuite%')
            ->setArgument('$stringFilter', '%paraunit.string_filter%');
    }
}
