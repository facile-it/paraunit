<?php
declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Coverage\CoverageResult;
use Paraunit\Coverage\Processor\Clover;
use Paraunit\Coverage\Processor\Crap4j;
use Paraunit\Coverage\Processor\Html;
use Paraunit\Coverage\Processor\Php;
use Paraunit\Coverage\Processor\Text;
use Paraunit\Coverage\Processor\TextToConsole;
use Paraunit\Coverage\Processor\Xml;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class CoverageConfiguration
 * @package Paraunit\Configuration
 */
class CoverageConfiguration extends ParallelConfiguration
{
    protected function loadYamlConfiguration(ContainerBuilder $containerBuilder): YamlFileLoader
    {
        $yamlLoader = parent::loadYamlConfiguration($containerBuilder);

        $yamlLoader->load('coverage.yml');
        $yamlLoader->load('coverage_configuration.yml');
        $yamlLoader->load('process_with_coverage.yml');

        return $yamlLoader;
    }

    protected function loadPostCompileSettings(ContainerBuilder $container, InputInterface $input)
    {
        parent::loadPostCompileSettings($container, $input);

        /** @var CoverageResult $coverageResult */
        $coverageResult = $container->get('paraunit.coverage.coverage_result');

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
            $textToConsole = new TextToConsole($input->getOption('ansi'));
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
