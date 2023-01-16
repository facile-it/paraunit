<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\RiskyParser;
use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class RiskyParserTest extends BaseUnitTestCase
{
    public function testParsingFoundResult(): void
    {
        $test = new Test('b');

        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(true);

        $resultContainer = $this->prophesize(TestResultContainer::class);
        $methodProphecy = $resultContainer->handleTestResult($process, Argument::allOf(
            Argument::type(TestResultWithMessage::class),
            Argument::that(fn (TestResultWithMessage $result) => $result->getFailureMessage() === 'Risky message')
        ));
        $methodProphecy->shouldNotBeCalled();

        $parser = new RiskyParser($resultContainer->reveal());

        $parsedResult = $parser->handleLogItem($process, new LogData(TestStatus::ConsideredRisky, $test, 'Risky message'));

        $this->assertNull($parsedResult);
        $this->assertTrue($process->isWaitingForTestResult(), 'Process incorrectly marked as no longer waiting for results');

        $methodProphecy->shouldBeCalledOnce();

        $parsedResult = $parser->handleLogItem($process, new LogData(TestStatus::Passed, $test, null));

        $this->assertInstanceOf(TestResultWithMessage::class, $parsedResult);
        $this->assertSame('Risky message', $parsedResult->getFailureMessage());
        $this->assertFalse($process->isWaitingForTestResult());
    }

    public function testPassingTestShouldNotBeIntercepted(): void
    {
        $test = new Test('b');
        $resultContainer = $this->prophesize(TestResultContainer::class);
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldNotBeCalled();
        $process = new StubbedParaunitProcess();

        $parser = new RiskyParser($resultContainer->reveal());

        $this->assertNull($parser->handleLogItem($process, new LogData(TestStatus::Prepared, $test, null)));
        $this->assertNull($parser->handleLogItem($process, new LogData(TestStatus::Passed, $test, null)));
        $this->assertTrue($process->isWaitingForTestResult());
    }
}
