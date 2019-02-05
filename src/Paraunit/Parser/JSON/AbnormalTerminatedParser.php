<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\TestResultFactory;

class AbnormalTerminatedParser extends GenericParser
{
    public function __construct(TestResultFactory $testResultFactory, TestResultHandlerInterface $testResultHandler)
    {
        parent::__construct($testResultFactory, $testResultHandler, LogFetcher::LOG_ENDING_STATUS);
    }
}
