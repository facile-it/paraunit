<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\TestWarning;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\WarningTriggered;

/**
 * @template-extends AbstractTestHookTestCase<TestWarning, WarningTriggered>
 */
class TestWarningTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): TestWarning
    {
        return new TestWarning();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::WarningTriggered;
    }

    protected function createEvent(): WarningTriggered
    {
        return new WarningTriggered(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            $this->getExpectedMessage(),
            __FILE__,
            1,
            false,
        );
    }

    /**
     * @return non-empty-string
     */
    protected function getExpectedMessage(): string
    {
        return 'test warning message';
    }
}
