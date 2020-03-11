<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Parser\JSON\Log;
use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

class TestResultFactory
{
    public function createFromLog(Log $log): PrintableTestResultInterface
    {
        if ($log->getStatus() === LogFetcher::LOG_ENDING_STATUS) {
            return new TestResultWithAbnormalTermination(
                $log->test,
                'Abnormal termination -- complete test output:'
            );
        }

        if ($log->getMessage()) {
            return new TestResultWithMessage($log->test, $log->getMessage());
        }

        return new MuteTestResult();
    }
}
