<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;

class RiskyParser extends GenericParser
{
    private ?LogData $riskyLogItem = null;

    public function __construct(TestResultHandlerInterface $testResultContainer)
    {
        parent::__construct($testResultContainer, TestStatus::ConsideredRisky);
    }

    public function handleLogItem(AbstractParaunitProcess $process, LogData $logItem): ?TestResultInterface
    {
        if (
            $this->riskyLogItem
            && $logItem->test === $this->riskyLogItem->test
            && $logItem->status === TestStatus::Passed
        ) {
            return parent::handleLogItem($process, $this->riskyLogItem);
        }

        if ($this->logMatches($logItem)) {
            $this->riskyLogItem = $logItem;
        }

        return null;
    }
}
