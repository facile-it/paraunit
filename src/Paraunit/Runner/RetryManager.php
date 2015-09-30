<?php

namespace Paraunit\Runner;

use Paraunit\Lifecycle\ProcessEvent;

/**
 * Class RetryManager.
 */
class RetryManager
{
    const MYSQL_LOCK_EXCEPTION = '/(Deadlock found|Lock wait timeout exceeded)/';
    const DOCTRINE_EM_CLOSED = '/(The EntityManager is closed)/';

    /**
     * @var int
     */
    protected $maxRetry;

    /**
     * @param int $maxRetry
     */
    public function __construct($maxRetry = 3)
    {
        $this->maxRetry = $maxRetry;
    }

    /**
     * @param ProcessEvent $processEvent
     *
     * @return bool
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
    {

        $process = $processEvent->getProcess();

        if ($process->getRetryCount() >= $this->maxRetry) {
            return false;
        }

        $deadlocks = array();
        @preg_match(self::MYSQL_LOCK_EXCEPTION, $process->getOutput(), $deadlocks);

        $entityManagerClosed = array();
        @preg_match(self::DOCTRINE_EM_CLOSED, $process->getOutput(), $entityManagerClosed);

        if (
            !empty($deadlocks) ||
            !empty($entityManagerClosed)
        ) {
            $process->markAsToBeRetried();
            $process->increaseRetryCount();
        }

        return $process->isToBeRetried();
    }
}
