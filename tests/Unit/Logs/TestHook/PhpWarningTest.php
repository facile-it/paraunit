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
    protected function createSubscriber(): PhpWarning
    {
        return new PhpWarning();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::WarningTriggered;
    }

    protected function createEvent(): PhpWarningTriggered
    {
        return new PhpWarningTriggered(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTest(),
            $this->getExpectedMessage(),
            __FILE__,
            1
        );
    }

    protected function getExpectedMessage(): string
    {
        return 'test warning message';
    }
}
