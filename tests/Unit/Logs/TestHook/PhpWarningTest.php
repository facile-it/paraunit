<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\PhpWarning;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\PhpWarningTriggered;

/**
 * @template-extends AbstractTestHookTestCase<PhpWarning, PhpWarningTriggered>
 */
class PhpWarningTest extends AbstractTestHookTestCase
{
    public function testIgnoredByBaseline(): void
    {
        $this->createSubscriber()->notify($this->createEvent(ignoredByBaseline: true));

        $logData = $this->getDeserializedLogData();
        $this->assertTrue($logData->isIgnoredByBaseline(), 'ignoredByBaseline flag should be true');
    }

    protected function createSubscriber(): PhpWarning
    {
        return new PhpWarning();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::WarningTriggered;
    }

    protected function createEvent(bool $ignoredByBaseline = false): PhpWarningTriggered
    {
        return new PhpWarningTriggered(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            $this->getExpectedMessage(),
            __FILE__,
            1,
            false,
            $ignoredByBaseline,
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
