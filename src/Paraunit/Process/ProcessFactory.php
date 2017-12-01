<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Symfony\Component\Process\Process;

/**
 * Class ProcessFactory
 * @package Paraunit\Process
 */
class ProcessFactory
{
    /** @var CommandLine */
    private $cliCommand;

    /** @var string[] */
    private $baseCommandLine;

    /** @var string[] */
    private $environmentVariables;

    /**
     * ProcessFactory constructor.
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
        $this->baseCommandLine = array_merge($this->cliCommand->getExecutable(), $this->cliCommand->getOptions($phpunitConfig));
        $this->environmentVariables = [
            EnvVariables::LOG_DIR => $tempFilenameFactory->getPathForLog(),
        ];
    }

    public function create(string $testFilePath): AbstractParaunitProcess
    {
        $process = new Process(
            array_merge($this->baseCommandLine, [$testFilePath], $this->cliCommand->getSpecificOptions($testFilePath)),
            null,
            $this->environmentVariables
        );
        
        if (method_exists($process, 'inheritEnvironmentVariables')) {
            // method added in 3.0
            $process->inheritEnvironmentVariables();
        }

        return new SymfonyProcessWrapper($process, $testFilePath);
    }
}
