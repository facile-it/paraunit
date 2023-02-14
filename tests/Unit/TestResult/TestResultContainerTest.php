<?php

declare(strict_types=1);

namespace Tests\Unit\TestResult;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;
use Tests\BaseUnitTestCase;

class TestResultContainerTest extends BaseUnitTestCase
{
    public function testGetFilenames(): void
    {
        $testResultContainer = new TestResultContainer();

        $testResultContainer->addTestResult(new TestResult(new Test('Foo'), TestOutcome::Passed));
        $testResultContainer->addTestResult(new TestResult(new Test('Bar'), TestOutcome::Failure));
        $testResultContainer->addTestResult(new TestResult(new Test('Bar'), TestOutcome::Failure));
        $testResultContainer->addTestResult(new TestResult(new Test('Baz'), TestOutcome::Passed));

        $this->assertCount(2, $testResultContainer->getFileNames(TestOutcome::Passed));
        $this->assertCount(1, $testResultContainer->getFileNames(TestOutcome::Failure));
    }

    public function testAddTestResultHandlesMessages(): void
    {
        $testResultContainer = new TestResultContainer();

        $testResultContainer->addTestResult(new TestResult(new Test('Foo'), TestOutcome::Passed));
        $testResultContainer->addTestResult(new TestResultWithMessage(new Test('Foo'), TestOutcome::Failure, 'Failure message'));

        $this->assertCount(0, $testResultContainer->getTestResults(TestOutcome::Passed));
        $failures = $testResultContainer->getTestResults(TestOutcome::Failure);
        $this->assertCount(1, $failures);
        $this->assertSame(TestOutcome::Failure, $failures[0]->status);
        $this->assertSame('Failure message', $failures[0]->message);
    }
}
