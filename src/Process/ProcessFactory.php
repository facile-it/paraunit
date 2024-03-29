<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\EnvVariables;
use Paraunit\Configuration\PassThrough;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageDriver;
use Symfony\Component\Process\Process as SymfonyProcess;

class ProcessFactory
{
    /** @var string[] */
    private readonly array $baseCommandLine;

    /** @var string[] */
    public readonly array $environmentVariables;

    public function __construct(
        private readonly CommandLine $cliCommand,
        PHPUnitConfig $phpunitConfig,
        TempFilenameFactory $tempFilenameFactory,
        private readonly ChunkSize $chunkSize,
        private readonly PassThrough $passThrough,
    ) {
        $this->baseCommandLine = array_merge($this->cliCommand->getExecutable(), $this->cliCommand->getOptions($phpunitConfig));
        $this->environmentVariables = [
            EnvVariables::LOG_DIR => $tempFilenameFactory->getPathForLog(),
            EnvVariables::XDEBUG_MODE => $this->getDesiredXdebugMode(),
        ];
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

    public function create(string $testFilePath): Process
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
                $this->passThrough->options,
                $this->cliCommand->getSpecificOptions($testFilePath)
            );
        }

        $process = new SymfonyProcess(
            $command,
            null,
            $this->environmentVariables
        );

        return new SymfonyProcessWrapper($process, $testFilePath);
    }
}
