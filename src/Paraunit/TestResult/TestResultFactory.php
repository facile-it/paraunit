<?php

namespace Paraunit\TestResult;

use Paraunit\Parser\JSONLogFetcher;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

/**
 * Class TestResultFactory
 * @package Paraunit\TestResult
 */
class TestResultFactory
{
    /** @var  TestResultFormat */
    private $format;

    /**
     * TestResultFactory constructor.
     * @param TestResultFormat $format
     */
    public function __construct(TestResultFormat $format)
    {
        $this->format = $format;
    }

    /**
     * @param \stdClass $log
     * @return PrintableTestResultInterface
     */
    public function createFromLog(\stdClass $log)
    {
        if (property_exists($log, 'status') && $log->status == JSONLogFetcher::LOG_ENDING_STATUS) {
            return new TestResultWithAbnormalTermination(
                $this->format,
                $log->test,
                'Abnormal termination -- complete test output:'
            );
        }

        if (! property_exists($log, 'message')) {
            return new MuteTestResult();
        }

        if (property_exists($log, 'trace')) {
            $result = new FullTestResult($this->format, $log->test, $log->message);
            $this->addTraceToResult($result, $log);

            return $result;
        }

        return new TestResultWithMessage($this->format, $log->test, $log->message);
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
