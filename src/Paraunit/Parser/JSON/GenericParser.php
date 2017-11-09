<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\TestResultFactory;

/**
 * Class GenericParser
 * @package Paraunit\Parser\JSON
 */
class GenericParser implements ParserChainElementInterface
{
    /** @var TestResultFactory */
    protected $testResultFactory;

    /** @var TestResultHandlerInterface */
    protected $testResultContainer;

    /** @var string */
    protected $status;

    /** @var string|null */
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
        string $status,
        string $messageStartsWith = null
    ) {
        $this->testResultFactory = $testResultFactory;
        $this->testResultContainer = $testResultContainer;
        $this->status = $status;
        $this->messageStartsWith = $messageStartsWith;
    }

    /**
     * {@inheritdoc}
     */
    public function handleLogItem(AbstractParaunitProcess $process, \stdClass $logItem)
    {
        if ($this->logMatches($logItem)) {
            $testResult = $this->testResultFactory->createFromLog($logItem);
            $this->testResultContainer->handleTestResult($process, $testResult);

            return $testResult;
        }

        return null;
    }

    protected function logMatches(\stdClass $log): bool
    {
        if (! property_exists($log, 'status')) {
            return false;
        }

        if ($log->status !== $this->status) {
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
