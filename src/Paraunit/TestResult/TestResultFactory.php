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
        // tODO
        if (property_exists($log, 'trace')) {
            return new FullTestResult($this->format->getTestResultSymbol());
        }
    }
}
