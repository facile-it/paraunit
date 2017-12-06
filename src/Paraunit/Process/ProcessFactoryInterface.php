<?php

declare(strict_types=1);

namespace Paraunit\Process;

/**
 * Class ProcessFactory
 * @package Paraunit\Process
 */
interface ProcessFactoryInterface
{
    public function create(string $testFilePath): AbstractParaunitProcess;
}
