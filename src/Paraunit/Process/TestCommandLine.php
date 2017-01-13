<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;

/**
 * Class TestCliCommand
 * @package Paraunit\Process
 */
class TestCommandLine implements CliCommandInterface
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
     * @return string
     */
    public function getExecutable()
    {
        return 'php ' . $this->phpUnitBin->getPhpUnitBin();
    }

    /**
     * @param PHPUnitConfig $config
     * @param string $uniqueId
     * @return string
     */
    public function getOptions(PHPUnitConfig $config, $uniqueId)
    {
        return '-c ' . $config->getFileFullPath()
            . ' --printer Paraunit\Parser\JSON\LogPrinter'
            . $this->createOptionsString($config);
    }

    private function createOptionsString(PHPUnitConfig $config)
    {
        $optionString = '';

        foreach ($config->getPhpunitOptions() as $option) {
            $optionString .= ' --' . $option->getName();
            if ($option->hasValue()) {
                $optionString .= '=' . $option->getValue();
            }
        }
        
        return $optionString;
    }
}
