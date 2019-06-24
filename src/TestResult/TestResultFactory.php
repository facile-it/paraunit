<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

class TestResultFactory
{
    public function createFromLog(\stdClass $log): PrintableTestResultInterface
    {
        if (property_exists($log, 'status') && $log->status === LogFetcher::LOG_ENDING_STATUS) {
            return new TestResultWithAbnormalTermination(
                $log->test,
                'Abnormal termination -- complete test output:'
            );
        }

        if (! property_exists($log, 'message')) {
            return new MuteTestResult();
        }

        if (property_exists($log, 'trace')) {
            return new FullTestResult($log->test, $log->message, $log->trace);
        }

        return new TestResultWithMessage($log->test, $log->message);
    }
}
