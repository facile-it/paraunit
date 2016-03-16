<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\TestResultFactory;

/**
 * Class AbstractParser
 * @package Paraunit\Parser
 */
class AbstractParser implements JSONParserChainElementInterface
{
    /** @var  TestResultFactory */
    protected $testResultFactory;

    /** @var  string */
    protected $status;

    /** @var  string */
    protected $messageStartsWith;

    /**
     * AbstractParser constructor.
     *
     * @param TestResultFactory $testResultFactory
     * @param string $status The status that the parser should catch
     * @param string | null $messageStartsWith The start of the message that the parser should look for, if any
     */
    public function __construct(TestResultFactory $testResultFactory, $status, $messageStartsWith = null)
    {
        $this->testResultFactory = $testResultFactory;
        $this->status = $status;
        $this->messageStartsWith = $messageStartsWith;
    }

    /**
     * {@inheritdoc}
     */
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        if ($this->logMatches($logItem)) {
            return $this->testResultFactory->createFromLog($logItem);
        }

        return null;
    }

    /**
     * @param \stdClass $log
     * @return bool
     */
    protected function logMatches(\stdClass $log)
    {
        if ( ! property_exists($log, 'status')) {
            return false;
        }

        if ($log->status != $this->status) {
            return false;
        }

        if (is_null($this->messageStartsWith)) {
            return true;
        }

        if ( ! property_exists($log, 'message')) {
            return false;
        }

        return 0 === strpos($log->message, $this->messageStartsWith);
    }
}
