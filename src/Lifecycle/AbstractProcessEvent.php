<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Paraunit\Process\AbstractParaunitProcess;

abstract class AbstractProcessEvent extends AbstractEvent
{
    /** @var AbstractParaunitProcess */
    private $process;

    public function __construct(AbstractParaunitProcess $process)
    {
        $this->process = $process;
    }

    public function getProcess(): AbstractParaunitProcess
    {
        return $this->process;
    }
}
