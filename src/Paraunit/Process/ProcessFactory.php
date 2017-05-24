<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitConfig;

/**
 * Class ProcessFactory
 * @package Paraunit\Process
 */
class ProcessFactory
{
    /** @var CliCommandInterface */
    private $cliCommand;

    /** @var PHPUnitConfig */
    private $phpunitConfig;

    /**
     * ProcessFactory constructor.
     * @param CliCommandInterface $cliCommand
     * @param PHPUnitConfig $phpunitConfig
     */
    public function __construct(CliCommandInterface $cliCommand, PHPUnitConfig $phpunitConfig)
    {
        $this->cliCommand = $cliCommand;
        $this->phpunitConfig = $phpunitConfig;
    }

    /**
     * @param string $testFilePath
     * @return ParaunitProcessInterface
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function createProcess(string $testFilePath): ParaunitProcessInterface
    {
        $uniqueId = $this->createUniqueId($testFilePath);
        $command = $this->createCommandLine($testFilePath, $uniqueId);

        return new SymfonyProcessWrapper($testFilePath, $command, $uniqueId);
    }

    private function createCommandLine(string $testFilePath, string $uniqueId): string
    {
        return $this->cliCommand->getExecutable()
            . ' ' . $this->cliCommand->getOptions($this->phpunitConfig, $uniqueId)
            . ' ' . $testFilePath;
    }

    private function createUniqueId(string $testFilePath): string
    {
        return md5($testFilePath);
    }
}
