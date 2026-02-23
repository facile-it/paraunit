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
        return LogStatus::PHPUnitDeprecation;
    }

    protected function createEvent(): PhpunitDeprecationTriggered
    {
        return new PhpunitDeprecationTriggered(
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
        return 'test message';
    }
}
