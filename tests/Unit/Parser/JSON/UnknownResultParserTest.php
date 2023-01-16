<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\UnknownResultParser;
use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class UnknownResultParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider statusesProvider
     */
    public function testHandleLogItemShouldCatchAnything(TestStatus $status): void
    {
        $log = new LogData($status, new Test('test'), 'message');

        $resultContainer = $this->prophesize(TestResultHandlerInterface::class);
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldBeCalled();

        $parser = new UnknownResultParser($resultContainer->reveal());

        $this->assertNotNull($parser->handleLogItem(new StubbedParaunitProcess(), $log));
    }

    /**
     * @return \Generator<array{TestStatus}>
     */
    public static function statusesProvider(): \Generator
    {
        foreach (TestStatus::cases() as $status) {
            yield [$status];
        }
    }
}
