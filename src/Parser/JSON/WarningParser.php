<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;

class WarningParser extends GenericParser
{
    public function __construct(TestResultHandlerInterface $testResultContainer)
    {
        parent::__construct($testResultContainer, TestStatus::WarningTriggered);
    }

    public function handleLogItem(AbstractParaunitProcess $process, LogData $logItem): ?TestResultInterface
    {
        $testResult = parent::handleLogItem($process, $logItem);
        if ($testResult) {
            $process->setWaitingForTestResult(true);
        }

        return $testResult;
    }
}
