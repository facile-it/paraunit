<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\UnknownResultParser;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class UnknownResultParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider statusesProvider
     */
    public function testHandleLogItemShouldCatchAnything(string $statuses): void
    {
        $log = new \stdClass();
        $log->status = $statuses;
        $log->message = 'message';

        $resultContainer = $this->prophesize(TestResultHandlerInterface::class);
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldBeCalled();

        $parser = new UnknownResultParser($resultContainer->reveal(), 'no-status-required');
        $this->assertNotNull($parser->handleLogItem(new StubbedParaunitProcess(), $log));
    }

    /**
     * @return string[][]
     */
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
