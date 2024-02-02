<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\PhpUnitWarning;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\PhpunitWarningTriggered;

/**
 * @template-extends AbstractTestHookTestCase<PhpUnitWarning, PhpunitWarningTriggered>
 */
class PhpUnitWarningTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): PhpUnitWarning
    {
        return new PhpUnitWarning();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::WarningTriggered;
    }

    protected function createEvent(): PhpunitWarningTriggered
    {
        return new PhpunitWarningTriggered(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            $this->getExpectedMessage(),
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
