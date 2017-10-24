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

    protected function loadPostCompileSettings(ContainerBuilder $container, InputInterface $input)
    {
        parent::loadPostCompileSettings($container, $input);

        /** @var CoverageResult $coverageResult */
        $coverageResult = $container->get(CoverageResult::class);

        if ($input->getOption('clover')) {
            $clover = new Clover(new OutputFile($input->getOption('clover')));
            $coverageResult->addCoverageProcessor($clover);
        }

        if ($input->getOption('xml')) {
            $xml = new Xml(new OutputPath($input->getOption('xml')));
            $coverageResult->addCoverageProcessor($xml);
        }

        if ($input->getOption('html')) {
            $html = new Html(new OutputPath($input->getOption('html')));
            $coverageResult->addCoverageProcessor($html);
        }

        if ($input->getOption('text')) {
            $text = new Text(new OutputFile($input->getOption('text')));
            $coverageResult->addCoverageProcessor($text);
        }

        if ($input->getOption('text-to-console')) {
            /** @var OutputInterface $output */
            $output = $container->get(OutputInterface::class);
            $textToConsole = new TextToConsole($output, (bool)$input->getOption('ansi'));
            $coverageResult->addCoverageProcessor($textToConsole);
        }

        if ($input->getOption('crap4j')) {
            $crap4j = new Crap4j(new OutputFile($input->getOption('crap4j')));
            $coverageResult->addCoverageProcessor($crap4j);
        }

        if ($input->getOption('php')) {
            $php = new Php(new OutputFile($input->getOption('php')));
            $coverageResult->addCoverageProcessor($php);
        }
    }
}
