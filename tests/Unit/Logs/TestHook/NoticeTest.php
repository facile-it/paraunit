<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\Notice;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\NoticeTriggered;

/**
 * @template-extends AbstractTestHookTestCase<Notice, NoticeTriggered>
 */
class NoticeTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): Notice
    {
        return new Notice();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Notice;
    }

    protected function createEvent(): NoticeTriggered
    {
        return new NoticeTriggered(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            $this->getExpectedMessage(),
            'testFile.php',
            123,
            false,
            false,
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
