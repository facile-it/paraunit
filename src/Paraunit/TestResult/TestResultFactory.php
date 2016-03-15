<?php

namespace Paraunit\TestResult;

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

    public function createFromLog(\stdClass $log)
    {
        if (property_exists($log, 'message')) {
            if (property_exists($log, 'trace')) {
                $result = new FullTestResult($this->format, $log->test, $log->message);
                $this->addTraceToResult($result, $log);

                return $result;
            } else {
                return new TestResultWithMessage($this->format, $log->test, $log->message);
            }
        }

        return new MuteTestResult($this->format);
    }

    /**
     * @param FullTestResult $result
     * @param \stdClass $log
     */
    private function addTraceToResult(FullTestResult $result, $log)
    {
        foreach ($log->trace as $traceStep) {
            $result->addTraceStep(new TraceStep($traceStep->file, $traceStep->line));
        }
    }
}
