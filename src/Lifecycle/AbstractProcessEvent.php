<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Paraunit\Process\Process;

abstract class AbstractProcessEvent
{
    public function __construct(private readonly Process $process) {}

    public function getProcess(): Process
    {
        return $this->process;
    }
}
