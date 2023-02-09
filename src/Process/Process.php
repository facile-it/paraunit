<?php

declare(strict_types=1);

namespace Paraunit\Process;

interface Process
{
    public function getOutput(): string;

    public function getErrorOutput(): string;

    public function isTerminated(): bool;

    public function getCommandLine(): string;

    public function getExitCode(): ?int;
}
