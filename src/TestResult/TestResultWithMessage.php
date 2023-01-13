<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Parser\ValueObject\Test;
use Paraunit\TestResult\Interfaces\FailureMessageInterface;

class TestResultWithMessage extends MuteTestResult implements FailureMessageInterface
{
    public function __construct(Test $test, private readonly string $failureMessage)
    {
        parent::__construct($test);
    }

    public function getFailureMessage(): string
    {
        return $this->failureMessage;
    }
}
