<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\PhpNotice;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\PhpNoticeTriggered;

/**
 * @template-extends AbstractTestHookTestCase<PhpNotice, PhpNoticeTriggered>
 */
class PhpNoticeTest extends AbstractTestHookTestCase
{
    public function testIgnoredByBaseline(): void
    {
        $this->createSubscriber()->notify($this->createEvent(ignoredByBaseline: true));

        $logData = $this->getDeserializedLogData();
        $this->assertTrue($logData->isIgnoredByBaseline(), 'ignoredByBaseline flag should be true');
    }

    protected function createSubscriber(): PhpNotice
    {
        return new PhpNotice();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Notice;
    }

    protected function createEvent(bool $ignoredByBaseline = false): PhpNoticeTriggered
    {
        return new PhpNoticeTriggered(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            $this->getExpectedMessage(),
            'testFile.php',
            123,
            false,
            $ignoredByBaseline,
        );
    }

    /**
     * @return non-empty-string
     */
    protected function getExpectedMessage(): string
    {
        return 'test message';
    }
}
