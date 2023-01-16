<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;

class UnknownResultParser extends GenericParser
{
    public function __construct(TestResultHandlerInterface $testResultContainer)
    {
        parent::__construct($testResultContainer, TestStatus::Errored);
    }

    protected function logMatches(LogData $log): bool
    {
        // catch 'em all!
        return true;
    }
}
