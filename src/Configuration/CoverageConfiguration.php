<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Configuration\DependencyInjection\CoverageContainerDefinition;
use Paraunit\Coverage\CoverageResult;
use Paraunit\Coverage\Processor\Clover;
use Paraunit\Coverage\Processor\CoverageProcessorInterface;
use Paraunit\Coverage\Processor\Crap4j;
use Paraunit\Coverage\Processor\Html;
use Paraunit\Coverage\Processor\Php;
use Paraunit\Coverage\Processor\Text;
use Paraunit\Coverage\Processor\TextSummary;
use Paraunit\Coverage\Processor\Xml;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CoverageConfiguration extends ParallelConfiguration
{
    public function __construct(bool $createPublicServiceAliases = false)
    {
        parent::__construct($createPublicServiceAliases);
        $this->containerDefinition = new CoverageContainerDefinition();
    }

    protected function loadCommandLineOptions(ContainerBuilder $containerBuilder, InputInterface $input): void
    {
        parent::loadCommandLineOptions($containerBuilder, $input);

        $coverageResult = $containerBuilder->getDefinition(CoverageResult::class);

        $this->addPathProcessor($coverageResult, $input, Xml::class);
        $this->addPathProcessor($coverageResult, $input, Html::class);

        $this->addFileProcessor($coverageResult, $input, Clover::class);
        $this->addFileOrOutputProcessor($coverageResult, $input, Text::class);
        $this->addFileOrOutputProcessor($coverageResult, $input, TextSummary::class);
        $this->addFileProcessor($coverageResult, $input, Crap4j::class);
        $this->addFileProcessor($coverageResult, $input, Php::class);
    }

    /**
     * @param mixed[] $dependencies
     */
    private function addProcessor(Definition $coverageResult, string $processorClass, array $dependencies): void
    {
        $coverageResult->addMethodCall('addCoverageProcessor', [new Definition($processorClass, $dependencies)]);
    }

    private function addFileProcessor(
        Definition $coverageResult,
        InputInterface $input,
        string $processorClass
    ): void {
        $optionName = $this->getOptionName($processorClass);

        if ($input->getOption($optionName)) {
            $this->addProcessor($coverageResult, $processorClass, [
                $this->createOutputFileDefinition($input, $optionName),
                (bool) $input->getOption('ansi'),
            ]);
        }
    }

    private function addFileOrOutputProcessor(
        Definition $coverageResult,
        InputInterface $input,
        string $processorClass
    ): void {
        $optionName = $this->getOptionName($processorClass);

        if ($this->optionIsEnabled($input, $optionName)) {
            $this->addProcessor($coverageResult, $processorClass, [
                new Reference(OutputInterface::class),
                (bool) $input->getOption('ansi'),
                $this->createOutputFileDefinition($input, $optionName),
            ]);
        }
    }

    private function addPathProcessor(
        Definition $coverageResult,
        InputInterface $input,
        string $processorClass
    ): void {
        $optionName = $this->getOptionName($processorClass);

        if ($this->optionIsEnabled($input, $optionName)) {
            $this->addProcessor($coverageResult, $processorClass, [
                new Definition(OutputPath::class, [$input->getOption($optionName)]),
            ]);
        }
    }

    private function createOutputFileDefinition(InputInterface $input, string $optionName): ?Definition
    {
        if ($input->getOption($optionName)) {
            return new Definition(OutputFile::class, [$input->getOption($optionName)]);
        }

        return null;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getOptionName(string $processorClass): string
    {
        $implements = class_implements($processorClass);
        if (is_array($implements) && \in_array(CoverageProcessorInterface::class, $implements, true)) {
            /** @var class-string<CoverageProcessorInterface> $processorClass */
            return $processorClass::getConsoleOptionName();
        }

        throw new \InvalidArgumentException('Expecting FQCN of class implementing ' . CoverageProcessorInterface::class . ', got ' . $processorClass);
    }

    private function optionIsEnabled(InputInterface $input, string $optionName): bool
    {
        return $input->hasParameterOption('--' . $optionName);
    }
}
