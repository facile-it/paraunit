<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\TestFinished;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\Finished;

/**
 * @template-extends AbstractTestHookTestCase<TestFinished, Finished>
 */
class TestFinishedTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): TestFinished
    {
        return new TestFinished();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Finished;
    }

    protected function createEvent(): Finished
    {
        return new Finished(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            1
        );
    }

    protected function getExpectedMessage(): ?string
    {
        return null;
    }
}
