<?php

declare(strict_types=1);

namespace Paraunit\Process;

interface Process
{
    public function getOutput(): string;

    public function getErrorOutput(): string;

    /**
     * @param positive-int $pipelineNumber
     */
    public function start(int $pipelineNumber): void;

    public function isTerminated(): bool;

    public function getCommandLine(): string;

    public function getExitCode(): ?int;

    public function getUniqueId(): string;

    public function isToBeRetried(): bool;

    public function markAsToBeRetried(): void;

    public function getFilename(): string;

    public function getRetryCount(): int;
}
