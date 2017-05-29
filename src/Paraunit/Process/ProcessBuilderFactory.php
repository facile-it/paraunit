<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitConfig;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class ProcessBuilderFactory
 * @package Paraunit\Process
 */
class ProcessBuilderFactory
{
    /** @var ProcessBuilder */
    private $builderPrototype;
    /** @var CliCommandInterface */
    private $cliCommand;

    /**
     * ProcessBuilderFactory constructor.
     * @param CliCommandInterface $cliCommand
     * @param PHPUnitConfig $phpunitConfig
     */
    public function __construct(CliCommandInterface $cliCommand, PHPUnitConfig $phpunitConfig)
    {
        $this->cliCommand = $cliCommand;
        $this->builderPrototype = new ProcessBuilder();

        foreach ($this->cliCommand->getExecutable() as $item) {
            $this->builderPrototype->add($item);
        }

        foreach ($this->cliCommand->getOptions($phpunitConfig) as $option) {
            $this->builderPrototype->add($option);
        }
    }

    /**
     * @param $testFilePath
     * @return ProcessBuilder
     */
    public function create(string $testFilePath): ProcessBuilder
    {
        $builder = clone $this->builderPrototype;
        $builder->add($testFilePath);
        foreach ($this->cliCommand->getSpecificOptions($testFilePath) as $specificOption) {
            $builder->add($specificOption);
        }

        return $builder;
    }
}
