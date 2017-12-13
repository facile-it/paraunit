<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\MuteTestResult;

/**
 * Class RetryParser
 * @package Paraunit\Parser\JSON
 */
class RetryParser
{
    /** @var TestResultHandlerInterface */
    private $testResultContainer;

    /** @var int */
    private $maxRetryCount;

    /** @var string */
    private $regexPattern;

    /**
     * RetryParser constructor.
     * @param TestResultHandlerInterface $testResultContainer
     * @param int $maxRetryCount
     */
    public function __construct(TestResultHandlerInterface $testResultContainer, int $maxRetryCount = 3)
    {
        $this->testResultContainer = $testResultContainer;
        $this->maxRetryCount = $maxRetryCount;

        $patterns = [
            'The EntityManager is closed',
            // MySQL
            'Deadlock found',
            'Lock wait timeout exceeded',
            // SQLite
            'General error: 5 database is locked',
        ];

        $this->regexPattern = $this->buildRegexPattern($patterns);
    }

    public function processWillBeRetried(AbstractParaunitProcess $process, array $logs): bool
    {
        if ($process->getRetryCount() >= $this->maxRetryCount) {
            return false;
        }

        foreach ($logs as $logItem) {
            if ($this->containsRetriableError($logItem)) {
                $process->markAsToBeRetried();
                $testResult = new MuteTestResult();
                $this->testResultContainer->handleTestResult($process, $testResult);

                return true;
            }
        }

        return false;
    }

    private function containsRetriableError(\stdClass $log): bool
    {
        return property_exists($log, 'status')
            && $log->status === 'error'
            && preg_match($this->regexPattern, $log->message);
    }

    /**
     * @param string[] $patterns
     * @return string
     */
    private function buildRegexPattern(array $patterns): string
    {
        $regex = implode('|', $patterns);

        return '/' . $regex . '/';
    }
}
