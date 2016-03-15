<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\FullTestResult;

/**
 * Class AbnormalTerminatedParser
 * @package Paraunit\Parser
 */
class AbnormalTerminatedParser extends AbstractParser
{
    /** @var TestStartParser */
    private $startParser;

    /**
     * AbnormalTerminatedParser constructor.
     * @param TestStartParser $startParser
     */
    public function __construct(TestStartParser $startParser)
    {
        $this->startParser = $startParser;
    }

    /**
     * {@inheritdoc}
     */
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        // TODO -- test end in coda al log
        $result = new FullTestResult('', $this->startParser->getLastFunctionByProcess($process), )

        return null;
    }

}
