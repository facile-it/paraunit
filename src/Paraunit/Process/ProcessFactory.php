<?php

namespace Paraunit\Process;

use Paraunit\Configuration\TempFileNameFactory;
use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfigFile;

/**
 * Class ProcessFactory
 * @package Paraunit\Process
 */
class ProcessFactory
{
    /** @var  CliCommandInterface */
    private $cliCommand;

    /** @var  PHPUnitConfigFile */
    private $phpunitConfigFile;

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
            . ' ' . $this->cliCommand->getOptions($this->phpunitConfigFile, $uniqueId)
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

    public function setPHPUnitConfigFile(PHPUnitConfigFile $phpunitConfigFile)
    {
        $this->phpunitConfigFile = $phpunitConfigFile;
    }
}
