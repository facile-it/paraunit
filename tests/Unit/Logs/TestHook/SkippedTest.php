<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\Skipped;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\Skipped as PHPUnitSkipped;

/**
 * @template-extends AbstractTestHookTestCase<Skipped, PHPUnitSkipped>
 */
class SkippedTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): Skipped
    {
        return new Skipped();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Skipped;
    }

    protected function createEvent(): PHPUnitSkipped
    {
        return new PHPUnitSkipped(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTest(),
            $this->getExpectedMessage(),
        );
    }

    protected function getExpectedMessage(): string
    {
        return 'skipped test message';
    }
}
