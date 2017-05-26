<?php
declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

/**
 * Class TestResultFactory
 * @package Paraunit\TestResult
 */
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
            $result = new FullTestResult($log->test, $log->message);
            $this->addTraceToResult($result, $log);

            return $result;
        }

        return new TestResultWithMessage($log->test, $log->message);
    }

    private function addTraceToResult(FullTestResult $result, \stdClass $log)
    {
        foreach ($log->trace as $traceStep) {
            $result->addTraceStep(new TraceStep($traceStep->file, $traceStep->line));
        }
    }
}
