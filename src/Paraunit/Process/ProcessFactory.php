<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitConfig;

/**
 * Class ProcessFactory
 * @package Paraunit\Process
 */
class ProcessFactory
{
    /** @var  CliCommandInterface */
    private $cliCommand;

    /** @var  PHPUnitConfig */
    private $phpunitConfig;

    /**
     * ProcessFactory constructor.
     * @param CliCommandInterface $cliCommand
     */
    public function __construct(CliCommandInterface $cliCommand)
    {
        $this->cliCommand = $cliCommand;
    }

    /**
     * @param $testFilePath
     * @return SymfonyProcessWrapper
     * @throws \Exception
     */
    public function createProcess($testFilePath)
    {
        $uniqueId = $this->createUniqueId($testFilePath);
        $command = $this->createCommandLine($testFilePath, $uniqueId);

        return new SymfonyProcessWrapper($command, $uniqueId);
    }

    /**
     * @param string $testFilePath
     * @param string $uniqueId
     * @return string
     */
    private function createCommandLine($testFilePath, $uniqueId)
    {
        return $this->cliCommand->getExecutable()
            . ' ' . $this->cliCommand->getOptions($this->phpunitConfig, $uniqueId)
            . ' ' . $testFilePath;
    }

    /**
     * @param string $testFilePath
     * @return string
     */
    private function createUniqueId($testFilePath)
    {
        return md5($testFilePath);
    }

    public function setPHPUnitConfig(PHPUnitConfig $phpunitConfig)
    {
        $this->phpunitConfig = $phpunitConfig;
    }
}
