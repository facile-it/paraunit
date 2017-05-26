<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Parser\JSON\LogPrinter;

/**
 * Class TestCliCommand
 * @package Paraunit\Process
 */
class TestCommandLine implements CliCommandInterface
{
    /** @var PHPUnitBinFile */
    protected $phpUnitBin;

    /**
     * TestCliCommand constructor.
     * @param PHPUnitBinFile $phpUnitBin
     */
    public function __construct(PHPUnitBinFile $phpUnitBin)
    {
        $this->phpUnitBin = $phpUnitBin;
    }

    public function getExecutable(): string
    {
        return 'php ' . $this->phpUnitBin->getPhpUnitBin();
    }

    public function getOptions(PHPUnitConfig $config, string $uniqueId): string
    {
        return '-c ' . $config->getFileFullPath()
            . sprintf(' --printer="%s"', LogPrinter::class)
            . $this->createOptionsString($config);
    }

    private function createOptionsString(PHPUnitConfig $config): string
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
