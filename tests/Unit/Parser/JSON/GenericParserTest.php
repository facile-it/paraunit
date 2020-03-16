<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\GenericParser;
use Paraunit\Parser\JSON\Log;
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
        string $statusToMatch,
        string $status,
        bool $shouldMatch
    ): void {
        $log = new Log($status, 'b', 'c');
        $result = new TestResultWithMessage('b', 'c');

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
     * @return (string|null|bool)[][]
     */
    public function matchesProvider(): array
    {
        return [
            ['error', 'error', true],
            ['error', 'pass', false],
        ];
    }
}
