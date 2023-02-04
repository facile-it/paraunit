<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\TestRunner\ExecutionStarted as TestSuiteExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use PHPUnit\Event\TestSuite\TestSuiteForTestClass;
use PHPUnit\Event\TestSuite\TestSuiteForTestMethodWithDataProvider;

class ExecutionStarted extends AbstractTestHook implements ExecutionStartedSubscriber
{
    public function notify(TestSuiteExecutionStarted $event): void
    {
        $this->write(LogStatus::Started, $this->createTest($event), (string) $event->testSuite()->count());
    }

    private function createTest(TestSuiteExecutionStarted $event): Test
    {
        $testSuite = $event->testSuite();

        $name = match (true) {
            $testSuite instanceof TestSuiteForTestMethodWithDataProvider => $testSuite->className(),
            $testSuite instanceof TestSuiteForTestClass => $testSuite->className(),
            default => $testSuite->name(),
        };

        return new Test($name);
    }
}
