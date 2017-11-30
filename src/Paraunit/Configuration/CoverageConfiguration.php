<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Configuration\DependencyInjection\CoverageContainerDefinition;
use Paraunit\Coverage\CoverageResult;
use Paraunit\Coverage\Processor\Clover;
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
    public function __construct()
    {
        parent::__construct();
        $this->containerDefinition = new CoverageContainerDefinition();
    }

    protected function loadCommandLineOptions(ContainerBuilder $container, InputInterface $input)
    {
        parent::loadCommandLineOptions($container, $input);

        $coverageResult = $container->getDefinition(CoverageResult::class);

        $this->addPathProcessor($coverageResult, $input, Xml::class, 'xml');
        $this->addPathProcessor($coverageResult, $input, Html::class, 'html');

        $this->addFileProcessor($coverageResult, $input, Clover::class, 'clover');
        $this->addFileProcessor($coverageResult, $input, Text::class, 'text');
        $this->addFileProcessor($coverageResult, $input, Crap4j::class, 'crap4j');
        $this->addFileProcessor($coverageResult, $input, Php::class, 'php');

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
        string $processorClass,
        string $optionName
    ) {
        if ($input->getOption($optionName)) {
            $this->addProcessor($coverageResult, $processorClass, [
                $this->createOutputFileDefinition($input, $optionName),
            ]);
        }
    }

    private function addPathProcessor(
        Definition $coverageResult,
        InputInterface $input,
        string $processorClass,
        string $optionName
    ) {
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
}
