<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\TestRunnerWarning;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\TestRunner\WarningTriggered;

/**
 * @template-extends AbstractTestHookTestCase<TestRunnerWarning, WarningTriggered>
 */
class TestRunnerWarningTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): TestRunnerWarning
    {
        return new TestRunnerWarning();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::WarningTriggered;
    }

    protected function createEvent(): WarningTriggered
    {
        return new WarningTriggered(
            $this->createTelemetryInfo(),
            $this->getExpectedMessage(),
        );
    }

    protected function updatesLastTest(): bool
    {
        return false;
    }

    protected function getExpectedMessage(): string
    {
        return 'test warning message';
    }
}
