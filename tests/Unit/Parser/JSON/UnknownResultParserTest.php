<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\Log;
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
        $log = new Log($statuses, 'test', 'message');

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
            [Log::STATUS_SUCCESSFUL],
            [Log::STATUS_TEST_START],
            [Log::STATUS_ERROR],
            [Log::STATUS_FAILURE],
            [Log::STATUS_INCOMPLETE],
            [Log::STATUS_RISKY],
            [Log::STATUS_SKIPPED],
            [Log::STATUS_SUCCESSFUL],
            ['qwerty'],
            ['trollingYou'],
        ];
    }
}
