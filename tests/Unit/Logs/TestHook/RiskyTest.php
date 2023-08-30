<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\Risky;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\ConsideredRisky;

/**
 * @template-extends AbstractTestHookTestCase<Risky, ConsideredRisky>
 */
class RiskyTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): Risky
    {
        return new Risky();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::ConsideredRisky;
    }

    protected function createEvent(): ConsideredRisky
    {
        return new ConsideredRisky(
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
        return 'risky test message';
    }
}
