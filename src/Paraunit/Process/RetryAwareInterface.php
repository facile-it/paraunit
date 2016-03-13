<?php

namespace Paraunit\Process;

/**
 * Interface RetryAwareInterface.
 */
interface RetryAwareInterface extends OutputAwareInterface
{
    /**
     * @return int
     */
    public function getRetryCount();

    public function increaseRetryCount();

    public function markAsToBeRetried();

    /**
     * @return bool
     */
    public function isToBeRetried();
}
