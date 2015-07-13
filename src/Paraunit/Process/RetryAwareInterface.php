<?php
namespace Paraunit\Process;

/**
 * Interface RetryAwareInterface
 * @package Paraunit\Process
 */
interface RetryAwareInterface extends OutputAwareInterface
{

    /**
     * @return int
     */
    public function getRetryCount();

    /**
     * @return null
     */
    public function increaseRetryCount();

    /**
     * @return null
     */
    public function markAsToBeRetried();

    /**
     * @return bool
     */
    public function isToBeRetried();

}
