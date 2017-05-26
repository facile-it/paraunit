<?php
declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\TestResult\Interfaces\FailureMessageInterface;
use Paraunit\TestResult\Interfaces\FunctionNameInterface;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

/**
 * Class TestResultWithMessage
 * @package Paraunit\TestResult
 */
class TestResultWithMessage extends MuteTestResult implements
    PrintableTestResultInterface,
    FunctionNameInterface,
    FailureMessageInterface
{
    /** @var string */
    private $functionName;

    /** @var string */
    private $failureMessage;

    /**
     * TestResultWithMessage constructor.
     * @param string $functionName
     * @param string $failureMessage
     */
    public function __construct(string $functionName, string $failureMessage)
    {
        parent::__construct();
        $this->functionName = $functionName;
        $this->failureMessage = $failureMessage;
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    public function getFailureMessage(): string
    {
        return $this->failureMessage;
    }
}
