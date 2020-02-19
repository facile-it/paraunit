<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\GenericParser;
use Paraunit\TestResult\FullTestResult;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultFactory;
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
        $log = new \stdClass();
        $log->status = $status;

        $result = new FullTestResult('b', 'c', 'trace');

        $factory = $this->prophesize(TestResultFactory::class);
        $factory->createFromLog($log)->willReturn($result);
        $resultContainer = $this->prophesize(TestResultContainer::class);
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldBeCalledTimes((int) $shouldMatch);

        $parser = new GenericParser($factory->reveal(), $resultContainer->reveal(), $statusToMatch);

        /** @var FullTestResult $result */
        $parsedResult = $parser->handleLogItem(new StubbedParaunitProcess(), $log);

        if ($shouldMatch) {
            $this->assertEquals($result, $parsedResult);
        } else {
            $this->assertNull($parsedResult);
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
