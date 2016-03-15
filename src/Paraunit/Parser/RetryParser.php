<?php

namespace Paraunit\Parser;

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

    /** @var  string */
    protected $testResultSymbol;

    /** @var  int */
    private $maxRetryCount;

    /** @var  string */
    private $regexPattern;

    /**
     * @param TestResultFormat $testResultFormat
     * @param string $status
     * @param int $maxRetryCount
     */
    public function __construct(TestResultFormat $testResultFormat, $status, $maxRetryCount = 3)
    {
        $this->status = $status;
        $this->testResultSymbol = $testResultFormat->getTestResultSymbol();
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

    public function parseLog(TestResultContainerInterface $process, \stdClass $log)
    {
        if ($this->isRetriable($process) && $this->isToBeRetried($log)) {
            $result = new MuteTestResult($this->testResultSymbol);
            /** @var RetryAwareInterface | TestResultContainerInterface $process */
            $process->markAsToBeRetried();
            $process->addTestResult($result);

            return $result;
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
