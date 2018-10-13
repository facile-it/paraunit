<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class ProcessBuilderFactory
 */
class ProcessBuilderFactory implements ProcessFactoryInterface
{
    /** @var ProcessBuilder */
    private $builderPrototype;

    /** @var CommandLine */
    private $cliCommand;

    /**
     * ProcessBuilderFactory constructor.
     *
     * @param CommandLine $cliCommand
     * @param PHPUnitConfig $phpunitConfig
     * @param TempFilenameFactory $tempFilenameFactory
     */
    public function __construct(
        CommandLine $cliCommand,
        PHPUnitConfig $phpunitConfig,
        TempFilenameFactory $tempFilenameFactory
    ) {
        $this->cliCommand = $cliCommand;
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

    public function create(string $testFilePath): AbstractParaunitProcess
    {
        $builder = clone $this->builderPrototype;
        $builder->add($testFilePath);
        foreach ($this->cliCommand->getSpecificOptions($testFilePath) as $specificOption) {
            $builder->add($specificOption);
        }

        return new SymfonyProcessWrapper($builder->getProcess(), $testFilePath);
    }
}
