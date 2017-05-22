<?php

namespace Paraunit\Process;

/**
 * Interface RetryAwareInterface
 * @package Paraunit\Process
 */
interface RetryAwareInterface extends OutputAwareInterface
{
    public function getRetryCount(): int;

    /**
     * @return void
     */
    public function increaseRetryCount();

    /**
     * @return void
     */
    public function markAsToBeRetried();

    public function isToBeRetried(): bool;
}
