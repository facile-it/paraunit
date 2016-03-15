<?php

namespace Paraunit\Parser;

use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultContainerInterface;
use Paraunit\TestResult\TestResultFactory;
use Paraunit\TestResult\TestResultInterface;

/**
 * Class AbstractParser
 * @package Paraunit\Parser
 */
class AbstractParser implements JSONParserChainElementInterface, OutputContainerBearerInterface
{
    /** @var  TestResultFactory */
    protected $testResultFactory;

    /** @var  TestResultContainer */
    protected $testResultContainer;

    /** @var  string */
    protected $status;

    /** @var  string */
    protected $messageStartsWith;

    /**
     * AbstractParser constructor.
     *
     * @param TestResultFactory $testResultFactory
     * @param TestResultContainer $outputContainer
     * @param string $status The status that the parser should catch
     * @param string | null $messageStartsWith The start of the message that the parser should look for, if any
     */
    public function __construct(TestResultFactory $testResultFactory, TestResultContainer $outputContainer, $status, $messageStartsWith = null)
    {
        $this->testResultContainer = $outputContainer;
        $this->testResultFactory = $testResultFactory;
        $this->testResultFactory->setResultSymbol($this->testResultContainer->getTestResultFormat()->getTestResultSymbol());
        $this->status = $status;
        $this->messageStartsWith = $messageStartsWith;
    }

    /**
     * @return TestResultContainer
     */
    public function getTestResultContainer()
    {
        return $this->testResultContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function parseLog(TestResultContainerInterface $process, \stdClass $log)
    {
        if ($this->logMatches($process, $log)) {
            $result = $this->testResultFactory->createFromLog($log);
            $this->storeTestResults($process, $result);

            return $result;
        }

        return null;
    }

    /**
     * @param TestResultContainerInterface $process
     * @param \stdClass $log
     * @return bool
     */
    protected function logMatches(TestResultContainerInterface $process, \stdClass $log)
    {
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

    /**
     * @param TestResultContainerInterface $process
     * @param TestResultInterface $result
     */
    protected function storeTestResults(TestResultContainerInterface $process, TestResultInterface $result)
    {
        $this->testResultContainer->addTestResult($result);
        $process->addTestResult($result);
    }
}
