<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\EnvVariables;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Symfony\Component\Process\Process;

class ProcessFactory implements ProcessFactoryInterface
{
    /** @var CommandLine */
    private $cliCommand;

    /** @var string[] */
    private $environmentVariables;

    /** @var ChunkSize */
    private $chunkSize;

    /** @var PHPUnitConfig */
    private $phpunitConfig;

    public function __construct(
        CommandLine $cliCommand,
        PHPUnitConfig $phpunitConfig,
        TempFilenameFactory $tempFilenameFactory,
        ChunkSize $chunkSize
    ) {
        $this->cliCommand = $cliCommand;
        $this->environmentVariables = [
            EnvVariables::LOG_DIR => $tempFilenameFactory->getPathForLog(),
        ];
        $this->chunkSize = $chunkSize;
        $this->phpunitConfig = $phpunitConfig;
    }

    public function create(string $testFilePath): AbstractParaunitProcess
    {
        if ($this->chunkSize->isChunked()) {
            $command = array_merge(
                array_merge($this->cliCommand->getExecutable(), $this->cliCommand->getOptions($this->phpunitConfig, ['testsuite'])),
                ['--configuration=' . $testFilePath],
                $this->cliCommand->getSpecificOptions($testFilePath)
            );
        } else {
            $command = array_merge(
                array_merge($this->cliCommand->getExecutable(), $this->cliCommand->getOptions($this->phpunitConfig)),
                [$testFilePath],
                $this->cliCommand->getSpecificOptions($testFilePath)
            );
        }
        $process = new Process(
            $command,
            null,
            $this->environmentVariables
        );

        return new SymfonyProcessWrapper($process, $testFilePath);
    }
}
