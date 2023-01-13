<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\WarningParser;
use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\TestResult\TestResultContainer;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class WarningParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider matchesProvider
     */
    public function testParsingFoundResult(TestStatus $status): void
    {
        $log = new LogData($status, new Test('b'), 'c');

        $resultContainer = $this->prophesize(TestResultContainer::class);
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldBeCalledTimes((int) ($status === TestStatus::WarningTriggered));
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
            yield $status->value => [$status];
        }
    }
}
