<?php

namespace Paraunit\TestResult;

use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

/**
 * Class TestResultFactory
 * @package Paraunit\TestResult
 */
class TestResultFactory
{
    /**
     * @param \stdClass $log
     * @return PrintableTestResultInterface
     */
    public function createFromLog(\stdClass $log)
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

    /**
     * @param FullTestResult $result
     * @param \stdClass $log
     */
    private function addTraceToResult(FullTestResult $result, \stdClass $log)
    {
        foreach ($log->trace as $traceStep) {
            $result->addTraceStep(new TraceStep($traceStep->file, $traceStep->line));
        }
    }
}
