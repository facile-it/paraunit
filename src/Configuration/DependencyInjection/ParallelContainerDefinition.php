<?php

declare(strict_types=1);

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PassThrough;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\File\Cleaner;
use Paraunit\File\TempDirectory;
use Paraunit\Filter\Filter;
use Paraunit\Logs\JSON\LogFetcher;
use Paraunit\Logs\JSON\LogHandler;
use Paraunit\Logs\JSON\LogParser;
use Paraunit\Logs\JSON\RetryParser;
use Paraunit\Printer\ConsoleFormatter;
use Paraunit\Printer\FailuresPrinter;
use Paraunit\Printer\FilesRecapPrinter;
use Paraunit\Printer\FinalPrinter;
use Paraunit\Printer\ProgressPrinter;
use Paraunit\Printer\SharkPrinter;
use Paraunit\Process\CommandLine;
use Paraunit\Process\ProcessFactory;
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
    public function configure(ContainerBuilder $container): ContainerBuilder
    {
        $container->setParameter('paraunit.max_retry_count', 3);

        foreach ($this->getAutowirableClasses() as $class) {
            $container->autowire($class);
        }

        $this->configureConfiguration($container);
        $this->configureEventDispatcher($container);
        $this->configurePrinter($container);
        $this->configureRunner($container);
        $this->configureServices($container);

        return $container;
    }

    /**
     * @return list<class-string>
     */
    protected function getAutowirableClasses(): array
    {
        // alphabetic order
        return [
            ChunkFile::class,
            ChunkSize::class,
            Cleaner::class,
            CommandLine::class,
            ConsoleFormatter::class,
            Facade::class,
            FailuresPrinter::class,
            FilesRecapPrinter::class,
            Filter::class,
            FinalPrinter::class,
            LogFetcher::class,
            LogHandler::class,
            LogParser::class,
            OutputFormatterInterface::class,
            PHPUnitBinFile::class,
            PHPUnitConfig::class,
            PipelineFactory::class,
            PipelineCollection::class,
            ProcessFactory::class,
            ProgressPrinter::class,
            RetryParser::class,
            SharkPrinter::class,
            TempDirectory::class,
            TempFilenameFactory::class,
            TestResultContainer::class,
        ];
    }

    private function configureConfiguration(ContainerBuilder $container): void
    {
        $container->autowire(PHPUnitConfig::class)
            ->setArgument('$inputPathOrFileName', '%paraunit.phpunit_config_filename%')
            ->setPublic(true);

        $container->autowire(ChunkSize::class)
            ->setArgument('$chunkSize', '%paraunit.chunk_size%');

        $container->register(PassThrough::class)
            ->setArguments(['%paraunit.pass_through%']);
    }

    private function configureEventDispatcher(ContainerBuilder $container): void
    {
        $container->autowire(SymfonyEventDispatcherInterface::class, EventDispatcher::class);
        $container->setAlias('event_dispatcher', SymfonyEventDispatcherInterface::class);
        $container->setAlias(PsrEventDispatcherInterface::class, SymfonyEventDispatcherInterface::class);

        $container->addCompilerPass(new RegisterListenersPass());
    }

    private function configurePrinter(ContainerBuilder $container): void
    {
        $container->autowire(SharkPrinter::class)
            ->setArgument('$showLogo', '%paraunit.show_logo%');

        $container->autowire(OutputFormatterInterface::class)
            ->setFactory([new Reference(OutputInterface::class), 'getFormatter']);
    }

    private function configureRunner(ContainerBuilder $container): void
    {
        $container->autowire(PipelineCollection::class)
            ->setArgument('$maxProcessNumber', '%paraunit.max_process_count%');
        $container->autowire(Runner::class)
            ->setPublic(true);
    }

    private function configureServices(ContainerBuilder $container): void
    {
        $container->register(OutputInterface::class, OutputInterface::class)
            ->setPublic(true)
            ->setSynthetic(true);

        $container->autowire(Filter::class)
            ->setArgument('$testSuiteFilter', '%paraunit.testsuite%')
            ->setArgument('$stringFilter', '%paraunit.string_filter%')
            ->setArgument('$excludeTestSuiteFilter', '%paraunit.exclude_testsuite%')
            ->setArgument('$testSuffix', '%paraunit.test_suffix%');

        $container->autowire(RetryParser::class)
            ->setArgument('$maxRetryCount', '%paraunit.max_retry_count%');
    }
}
