<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\NoticeTriggered;
use PHPUnit\Event\Test\NoticeTriggeredSubscriber;

/**
 * @template-extends AbstractTestHook<NoticeTriggered>
 */
class Notice extends AbstractTestHook implements NoticeTriggeredSubscriber
{
    public function notify(NoticeTriggered $event): void
    {
        $this->write(
            LogStatus::Notice,
            Test::fromPHPUnitTest($event->test()),
            $event->message(),
            ignoredByBaseline: $event->ignoredByBaseline(),
        );
    }
}
