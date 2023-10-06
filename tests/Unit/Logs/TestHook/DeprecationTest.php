<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\Deprecation;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Test\DeprecationTriggered;

/**
 * @template-extends AbstractTestHookTestCase<Deprecation, DeprecationTriggered>
 */
class DeprecationTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): Deprecation
    {
        return new Deprecation();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Deprecation;
    }

    protected function createEvent(): DeprecationTriggered
    {
        return new DeprecationTriggered(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            $this->getExpectedMessage(),
            'testFile.php',
            123,
            false,
            false,
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
