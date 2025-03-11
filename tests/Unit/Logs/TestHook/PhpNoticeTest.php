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
    protected function createSubscriber(): PhpNotice
    {
        return new PhpNotice();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Notice;
    }

    protected function createEvent(): PhpNoticeTriggered
    {
        return new PhpNoticeTriggered(
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
