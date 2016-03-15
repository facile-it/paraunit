<?php

namespace Paraunit\Parser;

use Paraunit\TestResult\TestResultFactory;

/**
 * Class AbnormalTerminatedParser
 * @package Paraunit\Parser
 */
class AbnormalTerminatedParser extends AbstractParser
{
    /**
     * AbnormalTerminatedParser constructor.
     * @param TestResultFactory $testResultFactory
     */
    public function __construct(TestResultFactory $testResultFactory)
    {
        parent::__construct($testResultFactory, JSONLogFetcher::LOG_ENDING_STATUS);
    }
}
