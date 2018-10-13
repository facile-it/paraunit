<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\TestResult\Interfaces\FailureMessageInterface;
use Paraunit\TestResult\Interfaces\FunctionNameInterface;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\StackTraceInterface;

/**
 * Class FullTestResult
 */
class FullTestResult extends TestResultWithMessage implements PrintableTestResultInterface, FunctionNameInterface, FailureMessageInterface, StackTraceInterface
{
    /** @var string */
    private $trace;

    /**
     * FullTestResult constructor.
     *
     * @param string $functionName
     * @param string $failureMessage
     * @param string $trace
     */
    public function __construct(string $functionName, string $failureMessage, string $trace)
    {
        parent::__construct($functionName, $failureMessage);
        $this->trace = $trace;
    }

    public function getTrace(): string
    {
        return $this->trace;
    }
}
