<?php

declare(strict_types=1);

namespace Paraunit\Logs\JSON;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\TestStatus;
use Paraunit\Printer\ValueObject\TestOutcome;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\TestResult;
use Paraunit\TestResult\TestResultContainer;

class RetryParser
{
    private readonly string $regexPattern;

    public function __construct(
        private readonly TestResultContainer $testResultContainer,
        private readonly int $maxRetryCount = 3
    ) {
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

    public function processWillBeRetried(AbstractParaunitProcess $process, LogData $log): bool
    {
        if ($process->getRetryCount() >= $this->maxRetryCount) {
            return false;
        }

        if (! $this->containsRetryableError($log)) {
            return false;
        }

        $testResult = new TestResult($log->test, TestOutcome::Retry);
        $this->testResultContainer->addTestResult($testResult);
        $process->addTestResult($testResult);
        $process->markAsToBeRetried();

        return true;
    }

    private function containsRetryableError(LogData $log): bool
    {
        return $log->status === TestStatus::Errored
            && $log->message
            && 1 === preg_match($this->regexPattern, $log->message);
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
