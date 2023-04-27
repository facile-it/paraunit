<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\Passed;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\Passed as PassedEvent;

/**
 * @template-extends AbstractTestHookTestCase<Passed, PassedEvent>
 */
class PassedTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): Passed
    {
        return new Passed();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Passed;
    }

    protected function createEvent(): PassedEvent
    {
        return new PassedEvent(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
        );
    }

    protected function getExpectedMessage(): ?string
    {
        return null;
    }
}
