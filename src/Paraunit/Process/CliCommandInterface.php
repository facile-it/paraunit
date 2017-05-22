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

    public function getOptions(PHPUnitConfig $config, string $uniqueId): string;
}
