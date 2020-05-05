<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\MuteTestResult;

class RetryParser
{
    /** @var TestResultHandlerInterface */
    private $testResultContainer;

    /** @var int */
    private $maxRetryCount;

    /** @var string */
    private $regexPattern;

    public function __construct(TestResultHandlerInterface $testResultContainer, int $maxRetryCount = 3)
    {
        $this->testResultContainer = $testResultContainer;
        $this->maxRetryCount = $maxRetryCount;

        $patterns = [
            'The EntityManager is closed',
            // MySQL
            'Deadlock found',
            'Lock wait timeout exceeded',
            'SAVEPOINT \w+ does not exist',
            // PostgreSQL
            'Deadlock detected',
            // SQLite
            'General error: 5 database is locked',
        ];

        $this->regexPattern = $this->buildRegexPattern($patterns);
    }

    /**
     * @param Log[] $logs
     */
    public function processWillBeRetried(AbstractParaunitProcess $process, array $logs): bool
    {
        if ($process->getRetryCount() >= $this->maxRetryCount) {
            return false;
        }

        foreach ($logs as $logItem) {
            if ($this->containsRetryableError($logItem)) {
                $process->markAsToBeRetried();
                $testResult = new MuteTestResult($logItem->getTest());
                $this->testResultContainer->handleTestResult($process, $testResult);

                return true;
            }
        }

        return false;
    }

    private function containsRetryableError(Log $log): bool
    {
        $message = $log->getMessage();

        return $log->getStatus() === Log::STATUS_ERROR
            && $message
            && preg_match($this->regexPattern, $message);
    }

    /**
     * @param string[] $patterns
     */
    private function buildRegexPattern(array $patterns): string
    {
        $regex = implode('|', $patterns);

        return '/' . $regex . '/';
    }
}
