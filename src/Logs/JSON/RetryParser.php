<?php

declare(strict_types=1);

namespace Paraunit\Logs\JSON;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Process\Process;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;

class RetryParser
{
    /** @var non-empty-string */
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

    /**
     * @param LogData[] $logs
     */
    public function processWillBeRetried(Process $process, array $logs): bool
    {
        if ($process->getRetryCount() >= $this->maxRetryCount) {
            return false;
        }

        foreach ($logs as $log) {
            if ($this->containsRetryableError($log)) {
                $testResult = new TestResult($log->test, TestOutcome::Retry);
                $this->testResultContainer->addTestResult($testResult);
                $process->markAsToBeRetried();

                return true;
            }
        }

        return false;
    }

    private function containsRetryableError(LogData $log): bool
    {
        return $log->status === LogStatus::Errored
            && $log->message
            && 1 === preg_match($this->regexPattern, $log->message);
    }

    /**
     * @param string[] $patterns
     *
     * @return non-empty-string
     */
    private function buildRegexPattern(array $patterns): string
    {
        $regex = implode('|', $patterns);

        return '/' . $regex . '/';
    }
}
