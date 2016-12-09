<?php

namespace Paraunit\Parser;

use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\TestResultFactory;

/**
 * Class AbnormalTerminatedParser
 * @package Paraunit\Parser
 */
class AbnormalTerminatedParser extends GenericParser
{
    /**
     * AbnormalTerminatedParser constructor.
     * @param TestResultFactory $testResultFactory
     * @param TestResultHandlerInterface $testResultHandler
     */
    public function __construct(TestResultFactory $testResultFactory, TestResultHandlerInterface $testResultHandler)
    {
        parent::__construct($testResultFactory, $testResultHandler, JSONLogFetcher::LOG_ENDING_STATUS);
    }
}
