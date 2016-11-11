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
     * @return string
     */
    public function getExecutable();

    /**
     * @param PHPUnitConfig $config
     * @param string $uniqueId
     * @return string
     */
    public function getOptions(PHPUnitConfig $config, $uniqueId);
}
