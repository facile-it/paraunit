<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\Failure;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Test\Failed;

/**
 * @template-extends AbstractTestHookTestCase<Failure, Failed>
 */
class FailureTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): Failure
    {
        return new Failure();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Failed;
    }

    protected function createEvent(): Failed
    {
        return new Failed(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTest(),
            Throwable::from(new \Exception($this->getExpectedMessage()[0])),
            null,
        );
    }

    /**
     * @return string[]
     */
    protected function getExpectedMessage(): array
    {
        return [
            'test failure message',
            'tests/Unit/Logs/TestHook/FailureTest.php',
            'tests/Unit/Logs/TestHook/AbstractTestHookTestCase.php',
        ];
    }
}
