<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Parser\JSON\LogPrinter;

/**
 * Class TestCliCommand
 * @package Paraunit\Process
 */
class CommandLine
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

    /**
     * @return string[]
     */
    public function getExecutable(): array
    {
        return ['php', $this->phpUnitBin->getPhpUnitBin()];
    }

    /**
     * @param PHPUnitConfig $config
     * @return array
     * @throws \RuntimeException When the config handling fails
     */
    public function getOptions(PHPUnitConfig $config): array
    {
        $options = [
            '--configuration=' . $config->getFileFullPath(),
            '--printer=' . LogPrinter::class,
        ];

        foreach ($config->getPhpunitOptions() as $phpunitOption) {
            $options[] = $this->buildPhpunitOptionString($phpunitOption);
        }

        return $options;
    }

    private function buildPhpunitOptionString(PHPUnitOption $option): string
    {
        $optionString = '--' . $option->getName();
        if ($option->hasValue()) {
            $optionString .= '=' . $option->getValue();
        }

        return $optionString;
    }

    public function getSpecificOptions(string $testFilename): array
    {
        return [];
    }
}
