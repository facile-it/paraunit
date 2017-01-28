<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitConfig;

/**
 * Interface CliCommandInterface
 * @package Paraunit\Process
 */
interface CliCommandInterface
{
    public function getExecutable(): string;

    /**
     * @param PHPUnitConfig $config
     * @return array
     */
    public function getOptions(PHPUnitConfig $config);

    /**
     * @param string $testFilename
     * @return array
     */
    public function getSpecificOptions($testFilename);
}
