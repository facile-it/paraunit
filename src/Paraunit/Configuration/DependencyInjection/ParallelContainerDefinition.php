<?php

declare(strict_types=1);

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\ParallelConfiguration;
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
use Paraunit\Printer\ProcessPrinter;
use Paraunit\Printer\SharkPrinter;
use Paraunit\Printer\SingleResultFormatter;
use Paraunit\Process\CommandLine;
use Paraunit\Process\ProcessBuilderFactory;
use Paraunit\Proxy\PHPUnitUtilXMLProxy;
use Paraunit\Runner\PipelineCollection;
use Paraunit\Runner\PipelineFactory;
use Paraunit\Runner\Runner;
use Paraunit\TestResult\TestResultList;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ParallelContainerDefinition
{
    /** @var ParserDefinition */
    private $parserDefinition;

    /** @var TestResultDefinition */
    private $testResult;

    public function __construct()
    {
        $this->parserDefinition = new ParserDefinition();
        $this->testResult = new TestResultDefinition();
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
        $this->testResult->configure($container);

        return $container;
    }

    private function configureConfiguration(ContainerBuilder $container)
    {
        $container->setDefinition(PHPUnitBinFile::class, new Definition(PHPUnitBinFile::class));
        $container->setDefinition(PHPUnitConfig::class, new Definition(PHPUnitConfig::class, [
            '%paraunit.phpunit_config_filename%',
        ]))
            ->setPublic(true);
        $container->setDefinition(TempFilenameFactory::class, new Definition(TempFilenameFactory::class, [
            new Reference(TempDirectory::class),
        ]));
    }

    private function configureEventDispatcher(ContainerBuilder $container)
    {
        if (class_exists('Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument')) {
            $container->setDefinition(EventDispatcherInterface::class, new Definition(EventDispatcher::class));
        } else {
            $container->setDefinition(
                EventDispatcherInterface::class,
                new Definition(ContainerAwareEventDispatcher::class, [new Reference('service_container')])
            );
        }

        $container->addCompilerPass(
            new RegisterListenersPass(
                EventDispatcherInterface::class,
                '',
                ParallelConfiguration::TAG_EVENT_SUBSCRIBER
            )
        );
    }

    private function configureFile(ContainerBuilder $container)
    {
        $container->setDefinition(TempDirectory::class, new Definition(TempDirectory::class));
        $container->setDefinition(Cleaner::class, new Definition(Cleaner::class, [
            new Reference(TempDirectory::class),
        ]));
    }

    private function configurePrinter(ContainerBuilder $container)
    {
        $output = new Reference(OutputInterface::class);

        $container->setDefinition(SharkPrinter::class, new Definition(SharkPrinter::class, [
            $output,
            '%paraunit.show_logo%',
        ]));
        $container->setDefinition(ProcessPrinter::class, new Definition(ProcessPrinter::class, [
            new Reference(SingleResultFormatter::class),
            $output,
        ]));

        $finalPrinterArguments = [
            new Reference(TestResultList::class),
            $output,
        ];
        $container->setDefinition(FinalPrinter::class, new Definition(FinalPrinter::class, $finalPrinterArguments));
        $container->setDefinition(FailuresPrinter::class, new Definition(FailuresPrinter::class, $finalPrinterArguments));
        $container->setDefinition(FilesRecapPrinter::class, new Definition(FilesRecapPrinter::class, $finalPrinterArguments));

        $container->setDefinition(ConsoleFormatter::class, new Definition(ConsoleFormatter::class, [$output]));
        $container->setDefinition(SingleResultFormatter::class, new Definition(SingleResultFormatter::class, [
            new Reference(TestResultList::class),
        ]));
    }

    private function configureProcess(ContainerBuilder $container)
    {
        $container->setDefinition(CommandLine::class, new Definition(CommandLine::class, [
            new Reference(PHPUnitBinFile::class),
        ]));

        $container->setDefinition(ProcessBuilderFactory::class, new Definition(ProcessBuilderFactory::class, [
            new Reference(CommandLine::class),
            new Reference(PHPUnitConfig::class),
            new Reference(TempFilenameFactory::class),
        ]));
    }

    private function configureRunner(ContainerBuilder $container)
    {
        $container->setDefinition(PipelineFactory::class, new Definition(PipelineFactory::class, [
            new Reference(EventDispatcherInterface::class),
        ]));
        $container->setDefinition(PipelineCollection::class, new Definition(PipelineCollection::class, [
            new Reference(PipelineFactory::class),
            '%paraunit.max_process_count%',
        ]));
        $container->setDefinition(Runner::class, new Definition(Runner::class, [
            new Reference(EventDispatcherInterface::class),
            new Reference(ProcessBuilderFactory::class),
            new Reference(Filter::class),
            new Reference(PipelineCollection::class),
        ]))
            ->setPublic(true);
    }

    private function configureServices(ContainerBuilder $container)
    {
        $container->setDefinition(PHPUnitUtilXMLProxy::class, new Definition(PHPUnitUtilXMLProxy::class));
        $container->setDefinition(\File_Iterator_Facade::class, new Definition(\File_Iterator_Facade::class));
        $container->setDefinition(Filter::class, new Definition(Filter::class, [
            new Reference(PHPUnitUtilXMLProxy::class),
            new Reference(\File_Iterator_Facade::class),
            new Reference(PHPUnitConfig::class),
            '%paraunit.testsuite%',
            '%paraunit.string_filter%',
        ]));
    }
}
