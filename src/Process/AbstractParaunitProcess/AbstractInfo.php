<?php

declare(strict_types=1);

namespace Paraunit\Process\AbstractParaunitProcess;

abstract class AbstractInfo
{
    // TODO - convert to interface?
    abstract public function getOutput(): string;

    abstract public function getErrorOutput(): string;

    abstract public function isTerminated(): bool;

    abstract public function getCommandLine(): string;

    abstract public function getExitCode(): ?int;
}
