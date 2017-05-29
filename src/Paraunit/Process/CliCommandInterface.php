<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitConfig;

/**
 * Interface CliCommandInterface
 * @package Paraunit\Process
 */
interface CliCommandInterface
{
    /**
     * @return string[]
     */
    public function getExecutable(): array;

    /**
     * @param PHPUnitConfig $config
     * @return string[]
     */
    public function getOptions(PHPUnitConfig $config): array;

    /**
     * @param string $testFilename
     * @return array
     */
    public function getSpecificOptions(string $testFilename): array;
}
