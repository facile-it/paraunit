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
use Paraunit\Coverage\Processor\TextToConsole;
use Paraunit\Coverage\Processor\Xml;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CoverageConfiguration
 * @package Paraunit\Configuration
 */
class CoverageConfiguration extends ParallelConfiguration
{
    public function __construct(bool $createPublicServiceAliases = false)
    {
        parent::__construct($createPublicServiceAliases);
        $this->containerDefinition = new CoverageContainerDefinition();
    }

    protected function loadCommandLineOptions(ContainerBuilder $container, InputInterface $input)
    {
        parent::loadCommandLineOptions($container, $input);

        $coverageResult = $container->getDefinition(CoverageResult::class);

        $this->addPathProcessor($coverageResult, $input, Xml::class);
        $this->addPathProcessor($coverageResult, $input, Html::class);

        $this->addFileProcessor($coverageResult, $input, Clover::class);
        $this->addFileProcessor($coverageResult, $input, Text::class);
        $this->addFileProcessor($coverageResult, $input, Crap4j::class);
        $this->addFileProcessor($coverageResult, $input, Php::class);

        if ($input->getOption('text-to-console')) {
            $this->addProcessor($coverageResult, TextToConsole::class, [
                new Reference(OutputInterface::class),
                (bool) $input->getOption('ansi'),
            ]);
        }
    }

    private function addProcessor(Definition $coverageResult, string $processorClass, array $dependencies)
    {
        $coverageResult->addMethodCall('addCoverageProcessor', [new Definition($processorClass, $dependencies)]);
    }

    private function addFileProcessor(
        Definition $coverageResult,
        InputInterface $input,
        string $processorClass
    ) {
        $optionName = $this->getOptionName($processorClass);

        if ($input->getOption($optionName)) {
            $this->addProcessor($coverageResult, $processorClass, [
                $this->createOutputFileDefinition($input, $optionName),
            ]);
        }
    }

    private function addPathProcessor(
        Definition $coverageResult,
        InputInterface $input,
        string $processorClass
    ) {
        $optionName = $this->getOptionName($processorClass);

        if ($input->getOption($optionName)) {
            $this->addProcessor($coverageResult, $processorClass, [
                $this->createOutputPathDefinition($input, $optionName),
            ]);
        }
    }

    private function createOutputFileDefinition(InputInterface $input, string $optionName): Definition
    {
        return new Definition(OutputFile::class, [$input->getOption($optionName)]);
    }

    private function createOutputPathDefinition(InputInterface $input, string $optionName): Definition
    {
        return new Definition(OutputPath::class, [$input->getOption($optionName)]);
    }

    /**
     * @param string $processorClass
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getOptionName(string $processorClass): string
    {
        if (! \in_array(CoverageProcessorInterface::class, class_implements($processorClass), true)) {
            throw new \InvalidArgumentException('Expecting FQCN of class implementing ' . CoverageProcessorInterface::class . ', got ' . $processorClass);
        }

        return $processorClass::getConsoleOptionName();
    }
}
