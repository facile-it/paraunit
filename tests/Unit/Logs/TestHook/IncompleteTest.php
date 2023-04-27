<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\Incomplete;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Test\MarkedIncomplete;

/**
 * @template-extends AbstractTestHookTestCase<Incomplete, MarkedIncomplete>
 */
class IncompleteTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): Incomplete
    {
        return new Incomplete();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::MarkedIncomplete;
    }

    protected function createEvent(): MarkedIncomplete
    {
        return new MarkedIncomplete(
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            new Throwable(\Exception::class, $this->getExpectedMessage(), 'Description', 'stack trace', null)
        );
    }

    protected function getExpectedMessage(): string
    {
        return 'incomplete test message';
    }
}
