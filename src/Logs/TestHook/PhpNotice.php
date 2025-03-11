<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggeredSubscriber;

/**
 * @template-extends AbstractTestHook<PhpNoticeTriggered>
 */
class PhpNotice extends AbstractTestHook implements PhpNoticeTriggeredSubscriber
{
    public function notify(PhpNoticeTriggered $event): void
    {
        $this->write(LogStatus::Notice, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
