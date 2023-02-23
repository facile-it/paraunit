<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\PhpUnitDeprecation;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\PhpunitDeprecationTriggered;

/**
 * @template-extends AbstractTestHookTestCase<PhpUnitDeprecation, PhpunitDeprecationTriggered>
 */
class PhpUnitDeprecationTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): PhpUnitDeprecation
    {
        return new PhpUnitDeprecation();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Deprecation;
    }

    protected function createEvent(): PhpUnitDeprecationTriggered
    {
        return new PhpUnitDeprecationTriggered(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTest(),
            $this->getExpectedMessage(),
        );
    }

    protected function getExpectedMessage(): string
    {
        return 'test message';
    }
}
