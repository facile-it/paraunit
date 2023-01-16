<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\EnvVariables;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageDriver;
use Symfony\Component\Process\Process;

class ProcessFactory implements ProcessFactoryInterface
{
    /** @var CommandLine */
    private $cliCommand;

    /** @var string[] */
    private $baseCommandLine;

    /** @var string[] */
    public readonly array $environmentVariables;

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
            EnvVariables::XDEBUG_MODE => $this->getDesiredXdebugMode(),
        ];
        $this->chunkSize = $chunkSize;
    }

    private function getDesiredXdebugMode(): string
    {
        if (! $this->cliCommand instanceof CommandLineWithCoverage) {
            return 'off';
        }

        if ($this->cliCommand->getCoverageDriver() !== CoverageDriver::Xdebug) {
            return 'off';
        }

        return 'coverage';
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

        return new SymfonyProcessWrapper($process, $testFilePath);
    }
}
