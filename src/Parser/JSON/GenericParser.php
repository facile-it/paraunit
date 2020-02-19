<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Paraunit\TestResult\TestResultFactory;

class GenericParser implements ParserChainElementInterface
{
    /** @var TestResultFactory */
    protected $testResultFactory;

    /** @var TestResultHandlerInterface */
    protected $testResultContainer;

    /** @var string */
    protected $status;

    /**
     * @param string $status The status that the parser should catch
     */
    public function __construct(
        TestResultFactory $testResultFactory,
        TestResultHandlerInterface $testResultContainer,
        string $status
    ) {
        $this->testResultFactory = $testResultFactory;
        $this->testResultContainer = $testResultContainer;
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function handleLogItem(AbstractParaunitProcess $process, \stdClass $logItem): ?TestResultInterface
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

        return $log->status === $this->status;
    }
}
