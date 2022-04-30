<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\EnvVariables;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

class ProcessFactory implements ProcessFactoryInterface
{
    /** @var CommandLine */
    private $cliCommand;

    /** @var string[] */
    private $baseCommandLine;

    /** @var string[] */
    private $environmentVariables;

    /** @var ChunkSize */
    private $chunkSize;

    public function __construct(
        CommandLine $cliCommand,
        PHPUnitConfig $phpunitConfig,
        TempFilenameFactory $tempFilenameFactory,
        ChunkSize $chunkSize
    ) {
        $this->cliCommand = $cliCommand;
        $this->baseCommandLine = array_merge($this->cliCommand->getExecutable(), $this->cliCommand->getOptions($phpunitConfig));
        $this->environmentVariables = [
            EnvVariables::LOG_DIR => $tempFilenameFactory->getPathForLog(),
        ];
        $this->chunkSize = $chunkSize;
    }

    public function create(string $testFilePath): AbstractParaunitProcess
    {
        if ($this->chunkSize->isChunked()) {
            $command = array_merge(
                $this->baseCommandLine,
                ['--configuration=' . $testFilePath],
                $this->cliCommand->getSpecificOptions($testFilePath)
            );
        } else {
            $command = array_merge(
                $this->baseCommandLine,
                [$testFilePath],
                $this->cliCommand->getSpecificOptions($testFilePath)
            );
        }
        $process = new Process(
            $command,
            null,
            $this->environmentVariables
        );

        if (method_exists(ProcessUtils::class, 'escapeArgument')) {
            // Symfony 3.4 BC layer
            $process->inheritEnvironmentVariables();
        }

        return new SymfonyProcessWrapper($process, $testFilePath);
    }
}
