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
    private $phpUnitBin;

    /** @var  TempFilenameFactory */
    protected $filenameFactory;

    /**
     * TestCliCommand constructor.
     * @param PHPUnitBinFile $phpUnitBin
     * @param TempFilenameFactory $filenameFactory
     */
    public function __construct(PHPUnitBinFile $phpUnitBin, TempFilenameFactory $filenameFactory)
    {
        $this->phpUnitBin = $phpUnitBin;
        $this->filenameFactory = $filenameFactory;
    }

    /**
     * @return string
     */
    public function getExecutable()
    {
        return $this->phpUnitBin->getPhpUnitBin();
    }

    /**
     * @param PHPUnitConfig $config
     * @param string $uniqueId
     * @return string
     */
    public function getOptions(PHPUnitConfig $config, $uniqueId)
    {
        return '-c ' . $config->getFileFullPath()
            . ' --log-json ' . $this->filenameFactory->getFilenameForLog($uniqueId)
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
