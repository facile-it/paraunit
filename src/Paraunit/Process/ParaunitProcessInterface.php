<?php

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
     * @return void
     */
    public function start();

    /**
     * @return void
     */
    public function restart();

    /**
     * @return void
     */
    public function reset();

    public function isRunning(): bool;

    /**
     * @return int|null
     */
    public function getExitCode();
}
