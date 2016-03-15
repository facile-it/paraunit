<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\Process\RetryAwareInterface;
use Paraunit\TestResult\MuteTestResult;
use Paraunit\TestResult\TestResultContainerInterface;
use Paraunit\TestResult\TestResultFormat;

/**
 * Class RetryParser
 * @package Paraunit\Parser
 */
class RetryParser implements JSONParserChainElementInterface
{
    /** @var  string */
    protected $status;

    /** @var  int */
    private $maxRetryCount;

    /** @var  string */
    private $regexPattern;

    /**
     * @param string $status
     * @param int $maxRetryCount
     */
    public function __construct($status, $maxRetryCount = 3)
    {
        $this->status = $status;
        $this->maxRetryCount = $maxRetryCount;

        $patterns = array(
            'The EntityManager is closed',
            // MySQL
            'Deadlock found',
            'Lock wait timeout exceeded',
            // SQLite
            'General error: 5 database is locked',
        );

        $this->regexPattern = $this->buildRegexPattern($patterns);
    }

    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        if ($this->isRetriable($process) && $this->isToBeRetried($logItem)) {
            /** @var RetryAwareInterface | TestResultContainerInterface $process */
            $process->markAsToBeRetried();

            return new MuteTestResult('?');
        }

        return null;
    }

    /**
     * @param TestResultContainerInterface $process
     * @return bool
     */
    private function isRetriable(TestResultContainerInterface $process)
    {
        return $process instanceof RetryAwareInterface && $process->getRetryCount() < $this->maxRetryCount;
    }

    /**
     * @param \stdClass $log
     * @return bool
     */
    private function isToBeRetried(\stdClass $log)
    {
        return $log->status == 'error' && preg_match($this->regexPattern, $log->message);
    }

    /**
     * @param array | string[] $patterns
     * @return string
     */
    private function buildRegexPattern(array $patterns)
    {
        $regex = implode('|', $patterns);

        return '/' . $regex . '/';
    }
}
