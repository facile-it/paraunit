<?php

namespace Paraunit\Process;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
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
     * @param TempFilenameFactory $tempFilenameFactory
     */
    public function __construct(
        CliCommandInterface $cliCommand,
        PHPUnitConfig $phpunitConfig,
        TempFilenameFactory $tempFilenameFactory
    ) {
        $this->cliCommand = $cliCommand;
        /** TODO inject builderPrototype so we can assert it's not returned but cloned, and with the right env vars */
        $this->builderPrototype = new ProcessBuilder();

        $this->builderPrototype->addEnvironmentVariables([
            EnvVariables::LOG_DIR => $tempFilenameFactory->getPathForLog(),
        ]);

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
