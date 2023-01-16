<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Paraunit\Process\AbstractParaunitProcess;

abstract class AbstractProcessEvent extends AbstractEvent
{
    public function __construct(private readonly AbstractParaunitProcess $process)
    {
    }

    public function getProcess(): AbstractParaunitProcess
    {
        return $this->process;
    }
}
