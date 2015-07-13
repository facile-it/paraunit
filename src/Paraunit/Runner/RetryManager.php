<?php
namespace Paraunit\Runner;

use Paraunit\Process\RetryAwareInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RetryManager
 * @package Paraunit\Runner
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
    function __construct($maxRetry = 3)
    {
        $this->maxRetry = $maxRetry;
    }

    /**
     * @param RetryAwareInterface $process
     * @return bool
     */
    public function setRetryStatus(RetryAwareInterface $process)
    {
        if ($process->getRetryCount() >= $this->maxRetry) {
            return false;
        }

        $deadlocks = [];
        @preg_match(self::MYSQL_LOCK_EXCEPTION, $process->getOutput(), $deadlocks);

        $entityManagerClosed = [];
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
