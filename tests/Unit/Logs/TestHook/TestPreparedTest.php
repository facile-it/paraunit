<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\TestPrepared;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\Prepared;

/**
 * @template-extends AbstractTestHookTestCase<TestPrepared, Prepared>
 */
class TestPreparedTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): TestPrepared
    {
        return new TestPrepared();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Prepared;
    }

    protected function createEvent(): Prepared
    {
        return new Prepared(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTest(),
        );
    }

    protected function getExpectedMessage(): ?string
    {
        return null;
    }
}
