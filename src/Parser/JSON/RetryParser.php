<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\MuteTestResult;

class RetryParser
{
    private readonly string $regexPattern;

    public function __construct(
        private readonly TestResultHandlerInterface $testResultContainer,
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
    public function processWillBeRetried(AbstractParaunitProcess $process, array $logs): bool
    {
        if ($process->getRetryCount() >= $this->maxRetryCount) {
            return false;
        }

        foreach ($logs as $logItem) {
            if ($this->containsRetryableError($logItem)) {
                $process->markAsToBeRetried();
                $testResult = new MuteTestResult($logItem->test);
                $this->testResultContainer->handleTestResult($process, $testResult);

                return true;
            }
        }

        return false;
    }

    private function containsRetryableError(LogData $log): bool
    {
        return $log->status === TestStatus::Errored
            && $log->message
            && preg_match($this->regexPattern, $log->message);
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
