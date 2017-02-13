<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;

/**
 * Class TestCliCommand
 * @package Paraunit\Process
 */
class CommandLine implements CliCommandInterface
{
    /** @var  PHPUnitBinFile */
    protected $phpUnitBin;

    /**
     * TestCliCommand constructor.
     * @param PHPUnitBinFile $phpUnitBin
     */
    public function __construct(PHPUnitBinFile $phpUnitBin)
    {
        $this->phpUnitBin = $phpUnitBin;
    }

    /**
     * @return string[]
     */
    public function getExecutable()
    {
        return array('php', $this->phpUnitBin->getPhpUnitBin());
    }

    /**
     * @param PHPUnitConfig $config
     * @return array
     * @throws \RuntimeException When the config handling fails
     */
    public function getOptions(PHPUnitConfig $config)
    {
        $options = array(
            '--configuration=' . $config->getFileFullPath(),
            '--printer=Paraunit\\Parser\\JSON\\LogPrinter',
        );

        foreach ($config->getPhpunitOptions() as $phpunitOption) {
            $options[] = $this->buildPhpunitOptionString($phpunitOption);
        }
        
        return $options;
    }

    private function buildPhpunitOptionString(PHPUnitOption $option)
    {
        $optionString = '--' . $option->getName();
        if ($option->hasValue()) {
            $optionString .= '=' . $option->getValue();
        }

        return $optionString;
    }

    public function getSpecificOptions($testFilename)
    {
        return array();
    }
}
