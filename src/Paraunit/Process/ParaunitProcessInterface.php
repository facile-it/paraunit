<?php
declare(strict_types=1);

namespace Paraunit\Process;

/**
 * Interface ParaunitProcessInterface
 * @package Paraunit\Process
 */
interface ParaunitProcessInterface
{
    public function isTerminated(): bool;

    public function getUniqueId(): string;

    public function getCommandLine(): string;

    /**
     * @param array $env An array of environment variables to be injected
     * @return void
     */
    public function start(array $env = []);

    /**
     * @return void
     */
    public function reset();

    public function isRunning(): bool;

    /**
     * @return int|null
     */
    public function getExitCode();

    /**
     * @return void
     */
    public function wait();
}
