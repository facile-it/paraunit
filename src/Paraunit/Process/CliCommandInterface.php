<?php
declare(strict_types=1);

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
     * @param string $uniqueId
     * @return array
     */
    public function getOptions(PHPUnitConfig $config, string $uniqueId): array;
}
