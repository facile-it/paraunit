<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\PhpUnitError;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\PhpunitErrorTriggered;

/**
 * @template-extends AbstractTestHookTestCase<PhpUnitError, PhpunitErrorTriggered>
 */
class PhpUnitErrorTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): PhpUnitError
    {
        return new PhpUnitError();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::ErrorTriggered;
    }

    protected function createEvent(): PhpunitErrorTriggered
    {
        return new PhpunitErrorTriggered(
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
        return 'test Error message';
    }
}
