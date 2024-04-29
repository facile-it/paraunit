<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\ExecutionStarted;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Code\TestCollection;
use PHPUnit\Event\TestRunner\ExecutionStarted as TestSuiteExecutionStarted;
use PHPUnit\Event\TestSuite\TestSuiteForTestClass;

/**
 * @template-extends AbstractTestHookTestCase<ExecutionStarted, TestSuiteExecutionStarted>
 */
class ExecutionStartedTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): ExecutionStarted
    {
        return new ExecutionStarted();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Started;
    }

    protected function createEvent(): TestSuiteExecutionStarted
    {
        return new TestSuiteExecutionStarted(
            $this->createTelemetryInfo(),
            new TestSuiteForTestClass(self::class, 0, TestCollection::fromArray([]), __FILE__, 0),
        );
    }

    protected function getExpectedTestObject(): Test
    {
        return new Test(static::class);
    }

    protected function getExpectedMessage(): ?string
    {
        return '0';
    }
}
