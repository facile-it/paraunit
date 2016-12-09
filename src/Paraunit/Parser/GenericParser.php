<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\TestResultFactory;

/**
 * Class GenericParser
 * @package Paraunit\Parser
 */
class GenericParser implements JSONParserChainElementInterface
{
    /** @var  TestResultFactory */
    protected $testResultFactory;

    /** @var  TestResultHandlerInterface */
    protected $testResultContainer;

    /** @var  string */
    protected $status;

    /** @var  string */
    protected $messageStartsWith;

    /**
     * GenericParser constructor.
     *
     * @param TestResultFactory $testResultFactory
     * @param TestResultHandlerInterface $testResultContainer
     * @param string $status The status that the parser should catch
     * @param string | null $messageStartsWith The start of the message that the parser should look for, if any
     */
    public function __construct(
        TestResultFactory $testResultFactory,
        TestResultHandlerInterface $testResultContainer,
        $status,
        $messageStartsWith = null
    ) {
        $this->testResultFactory = $testResultFactory;
        $this->testResultContainer = $testResultContainer;
        $this->status = $status;
        $this->messageStartsWith = $messageStartsWith;
    }

    /**
     * {@inheritdoc}
     */
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        if ($this->logMatches($logItem)) {
            $testResult = $this->testResultFactory->createFromLog($logItem);
            $this->testResultContainer->handleTestResult($process, $testResult);
            
            return $testResult;
        }

        return null;
    }

    /**
     * @param \stdClass $log
     * @return bool
     */
    protected function logMatches(\stdClass $log)
    {
        if (! property_exists($log, 'status')) {
            return false;
        }

        if ($log->status != $this->status) {
            return false;
        }

        if (null === $this->messageStartsWith) {
            return true;
        }

        if (! property_exists($log, 'message')) {
            return false;
        }

        return 0 === strpos($log->message, $this->messageStartsWith);
    }
}
