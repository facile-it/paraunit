<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\Error;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Test\Errored;

/**
 * @template-extends AbstractTestHookTestCase<Error, Errored>
 */
class ErrorTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): Error
    {
        return new Error();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Errored;
    }

    protected function createEvent(): Errored
    {
        return new Errored(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTest(),
            Throwable::from(new \Exception('test exception message')),
        );
    }

    /**
     * @return string[]
     */
    protected function getExpectedMessage(): array
    {
        return [
            'test exception message',
            'tests/Unit/Logs/TestHook/ErrorTest.php',
            'tests/Unit/Logs/TestHook/AbstractTestHookTestCase.php',
        ];
    }
}
