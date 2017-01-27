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

    /**
     * ProcessBuilderFactory constructor.
     * @param CliCommandInterface $cliCommand
     * @param PHPUnitConfig $phpunitConfig
     */
    public function __construct(CliCommandInterface $cliCommand, PHPUnitConfig $phpunitConfig)
    {
        $this->builderPrototype = new ProcessBuilder();

        $this->builderPrototype->setPrefix('php');
        $this->builderPrototype->add($cliCommand->getExecutable());

        foreach ($cliCommand->getOptions($phpunitConfig) as $option => $value) {
            $this->builderPrototype->add($option . ' ' . $value);
        }
    }

    /**
     * @param $testFilePath
     * @return ProcessBuilder
     */
    public function create($testFilePath)
    {
        $builder = clone $this->builderPrototype;
        $builder->add($testFilePath);

        return $builder;
    }
}
