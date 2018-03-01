<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Symfony\Component\Process\Process;
use Paraunit\Configuration\EnvVariables;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;

/**
 * Class ProcessBuilderFactory
 * @package Paraunit\Process
 */
class ProcessBuilderFactory implements ProcessFactoryInterface
{
    /** @var CommandLine */
    private $cliCommand;
    /** @var array */
    private $processArguments;
    /** @var array */
    private $processEnv;

    /**
     * ProcessBuilderFactory constructor.
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

        $this->processEnv = [
            EnvVariables::LOG_DIR => $tempFilenameFactory->getPathForLog(),
        ];

        foreach ($this->cliCommand->getExecutable() as $item) {
            $this->processArguments[] = $item;
        }

        foreach ($this->cliCommand->getOptions($phpunitConfig) as $option) {
            $this->processArguments[] = $option;
        }
    }

    public function create(string $testFilePath): AbstractParaunitProcess
    {
        $arguments = array_merge($this->processArguments, [$testFilePath]);

        foreach ($this->cliCommand->getSpecificOptions($testFilePath) as $specificOption) {
            $arguments[] = $specificOption;
        }

        return new SymfonyProcessWrapper(
            new Process($arguments, null, $this->processEnv),
            $testFilePath
        );
    }
}
