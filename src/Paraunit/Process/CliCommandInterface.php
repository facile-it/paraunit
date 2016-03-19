<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitConfigFile;

/**
 * Interface CliCommandInterface
 * @package Paraunit\Process
 */
interface CliCommandInterface
{
    /**
     * @return string
     */
    public function getExecutable();

    /**
     * @param PHPUnitConfigFile $configFile
     * @param string $uniqueId
     * @return string
     */
    public function getOptions(PHPUnitConfigFile $configFile, $uniqueId);
}
