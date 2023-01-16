<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\WarningParser;
use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class WarningParserTest extends BaseUnitTestCase
{
    public function testParsingWarning(): void
    {
        $log = new LogData(TestStatus::WarningTriggered, new Test('b'), 'Warning message');

        $resultContainer = $this->prophesize(TestResultContainer::class);
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldBeCalledOnce();
        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(true);

        $parser = new WarningParser($resultContainer->reveal());

        $parsedResult = $parser->handleLogItem($process, $log);

        $this->assertInstanceOf(TestResultWithMessage::class, $parsedResult);
        $this->assertEquals('Warning message', $parsedResult->getFailureMessage());
        $this->assertTrue($process->isWaitingForTestResult(), 'Process incorrectly marked as no longer waiting for results');
    }

    /**
     * @dataProvider matchesProvider
     */
    public function testParsingWithOtherStatues(TestStatus $status): void
    {
        $log = new LogData($status, new Test('b'), 'c');

        $resultContainer = $this->prophesize(TestResultContainer::class);
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldNotBeCalled();
        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(true);

        $parser = new WarningParser($resultContainer->reveal());

        $parsedResult = $parser->handleLogItem($process, $log);

        $this->assertNull($parsedResult);
        $this->assertTrue($process->isWaitingForTestResult(), 'Process incorrectly marked as no longer waiting for results');
    }

    /**
     * @return \Generator<array{TestStatus}>
     */
    public static function matchesProvider(): \Generator
    {
        foreach (TestStatus::cases() as $status) {
            if ($status === TestStatus::WarningTriggered) {
                continue;
            }

            yield $status->value => [$status];
        }
    }
}
