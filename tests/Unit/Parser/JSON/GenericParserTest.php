<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\GenericParser;
use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class GenericParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider matchesProvider
     */
    public function testParsingFoundResult(
        TestStatus $statusToMatch,
        TestStatus $status,
        bool $shouldMatch
    ): void {
        $log = new LogData($status, new Test('b'), 'c');
        $result = new TestResultWithMessage(new Test('b'), 'c');

        $resultContainer = $this->prophesize(TestResultContainer::class);
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldBeCalledTimes((int) $shouldMatch);
        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(true);

        $parser = new GenericParser($resultContainer->reveal(), $statusToMatch);

        $parsedResult = $parser->handleLogItem($process, $log);

        if ($shouldMatch) {
            $this->assertEquals($result, $parsedResult);
            $this->assertFalse($process->isWaitingForTestResult(), 'Process not marked as no longer waiting for results');
        } else {
            $this->assertNull($parsedResult);
            $this->assertTrue($process->isWaitingForTestResult(), 'Process incorrectly marked as no longer waiting for results');
        }
    }

    /**
     * @return array{TestStatus, TestStatus, bool}[]
     */
    public static function matchesProvider(): array
    {
        return [
            [TestStatus::Errored, TestStatus::Errored, true],
            [TestStatus::Errored, TestStatus::Passed, false],
        ];
    }
}
