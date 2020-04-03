<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\TestResult\Interfaces\FailureMessageInterface;

class TestResultWithMessage extends MuteTestResult implements FailureMessageInterface
{
    /** @var string */
    private $failureMessage;

    public function __construct(string $testName, string $failureMessage)
    {
        parent::__construct($testName);
        $this->failureMessage = $failureMessage;
    }

    public function getFailureMessage(): string
    {
        return $this->failureMessage;
    }
}
