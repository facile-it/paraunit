<?php

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\UnknownResultParser;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\TestResultFactory;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class UnknownResultParserTest
 * @package Tests\Unit\Parser\JSON
 */
class UnknownResultParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider statusesProvider
     */
    public function testHandleLogItemShouldCatchAnything(string $statuses)
    {
        $log = new \stdClass();
        $log->status = $statuses;
        $log->message = 'message';

        $factory = $this->prophesize(TestResultFactory::class);
        $factory->createFromLog($log)
            ->shouldBeCalled()
            ->willReturn($this->mockPrintableTestResult());
        $resultContainer = $this->prophesize(TestResultHandlerInterface::class);
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldBeCalled();

        $parser = new UnknownResultParser($factory->reveal(), $resultContainer->reveal(), 'no-status-required');
        $this->assertNotNull($parser->handleLogItem(new StubbedParaunitProcess(), $log));
    }

    public function statusesProvider(): array
    {
        return [
            ['pass'],
            ['error'],
            ['fail'],
            ['pass'],
            ['testStart'],
            ['suiteStart'],
            ['qwerty'],
            ['trollingYou'],
        ];
    }
}
