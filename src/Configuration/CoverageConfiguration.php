<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Configuration\DependencyInjection\CoverageContainerDefinition;
use Paraunit\Coverage\CoverageResult;
use Paraunit\Coverage\PhpUnitFacadeFactory;
use Paraunit\Coverage\Processor\Clover;
use Paraunit\Coverage\Processor\Cobertura;
use Paraunit\Coverage\Processor\CoverageProcessorInterface;
use Paraunit\Coverage\Processor\Crap4j;
use Paraunit\Coverage\Processor\Html;
use Paraunit\Coverage\Processor\Php;
use Paraunit\Coverage\Processor\Text;
use Paraunit\Coverage\Processor\TextSummary;
use Paraunit\Coverage\Processor\Xml;
use SebastianBergmann\CodeCoverage\Serialization\Serializer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CoverageConfiguration extends ParallelConfiguration
{
    public function __construct(?bool $createPublicServiceAliases = false)
    {
        parent::__construct($createPublicServiceAliases ?? false);
        $this->containerDefinition = new CoverageContainerDefinition();
    }

    protected function loadCommandLineOptions(ContainerBuilder $containerBuilder, InputInterface $input): void
    {
        parent::loadCommandLineOptions($containerBuilder, $input);

        $containerBuilder->autowire(PhpUnitFacadeFactory::class);
        $containerBuilder->autowire(Serializer::class);

        $this->addPathProcessor($containerBuilder, $input, Xml::class);
        $this->addPathProcessor($containerBuilder, $input, Html::class);

        $this->addFileProcessor($containerBuilder, $input, Clover::class);
        $this->addFileOrOutputProcessor($containerBuilder, $input, Text::class);
        $this->addFileOrOutputProcessor($containerBuilder, $input, TextSummary::class);
        $this->addFileProcessor($containerBuilder, $input, Crap4j::class);
        $this->addFileProcessor($containerBuilder, $input, Php::class);
        $this->addFileProcessor($containerBuilder, $input, Cobertura::class);
    }

    /**
     * @param class-string<CoverageProcessorInterface> $processorClass
     */
    private function addProcessor(ContainerBuilder $containerBuilder, string $processorClass): Definition
    {
        $coverageResult = $containerBuilder->getDefinition(CoverageResult::class);

        $processor = $containerBuilder->autowire($processorClass);
        $coverageResult->addMethodCall('addCoverageProcessor', [$processor]);

        return $processor;
    }

    /**
     * @param class-string<CoverageProcessorInterface> $processorClass
     */
    private function addFileProcessor(
        ContainerBuilder $containerBuilder,
        InputInterface $input,
        string $processorClass,
    ): void {
        $optionName = $processorClass::getConsoleOptionName();

        if ($input->getOption($optionName)) {
            $this->addProcessor($containerBuilder, $processorClass)
                ->setArgument('$targetFile', $this->createOutputFileDefinition($input, $optionName))
            ;
        }
    }

    /**
     * @param class-string<CoverageProcessorInterface> $processorClass
     */
    private function addFileOrOutputProcessor(
        ContainerBuilder $containerBuilder,
        InputInterface $input,
        string $processorClass,
    ): void {
        $optionName = $processorClass::getConsoleOptionName();

        if ($this->optionIsEnabled($input, $optionName)) {
            $this->addProcessor($containerBuilder, $processorClass)
                ->setArgument('$targetFile', $this->createOutputFileDefinition($input, $optionName))
                ->setArgument('$showColors', (bool) $input->getOption('ansi'))
            ;
        }
    }

    /**
     * @param class-string<CoverageProcessorInterface> $processorClass
     */
    private function addPathProcessor(
        ContainerBuilder $containerBuilder,
        InputInterface $input,
        string $processorClass,
    ): void {
        $optionName = $processorClass::getConsoleOptionName();

        if ($this->optionIsEnabled($input, $optionName)) {
            $this->addProcessor($containerBuilder, $processorClass)
                ->setArgument('$targetPath', new Definition(OutputPath::class, [$input->getOption($optionName)]))
            ;
        }
    }

    private function createOutputFileDefinition(InputInterface $input, string $optionName): ?Definition
    {
        if ($input->getOption($optionName)) {
            return new Definition(OutputFile::class, [$input->getOption($optionName)]);
        }

        return null;
    }

    private function optionIsEnabled(InputInterface $input, string $optionName): bool
    {
        return $input->hasParameterOption('--' . $optionName);
    }
}
