<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\JSON;

use Paraunit\Parser\JSON\AbnormalTerminatedParser;
use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\TestResult\NullTestResult;
use Paraunit\TestResult\TestWithAbnormalTermination;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\StubbedParaunitProcess;

class AbnormalTerminatedParserTest extends BaseFunctionalTestCase
{
    public function testHandleLogItemWithAbnormalTermination(): void
    {
        $process = new StubbedParaunitProcess();
        $logStart = new LogData(TestStatus::Prepared, new Test('Foo'), null);
        $logEnding = new LogData(TestStatus::LogTerminated, Test::unknown(), null);
        /** @var AbnormalTerminatedParser $parser */
        $parser = $this->getService(AbnormalTerminatedParser::class);

        $firstParsedResult = $parser->handleLogItem($process, $logStart);

        $this->assertInstanceOf(NullTestResult::class, $firstParsedResult);
        $this->assertTrue($process->isWaitingForTestResult());

        $secondParsedResult = $parser->handleLogItem($process, $logEnding);

        $this->assertInstanceOf(TestWithAbnormalTermination::class, $secondParsedResult);
        $this->assertFalse($process->isWaitingForTestResult());
    }

    /**
     * @dataProvider otherStatusesProvider
     */
    public function testHandleLogItemWithUncaughtLog(TestStatus $otherStatuses): void
    {
        $process = new StubbedParaunitProcess();
        $log = new LogData($otherStatuses, new Test('Foo'), null);
        /** @var AbnormalTerminatedParser $parser */
        $parser = $this->getService(AbnormalTerminatedParser::class);

        $parsedResult = $parser->handleLogItem($process, $log);

        $this->assertNull($parsedResult);
    }

    /**
     * @return \Generator<array{TestStatus}>
     */
    public static function otherStatusesProvider(): \Generator
    {
        foreach (TestStatus::cases() as $status) {
            if (in_array($status, [TestStatus::Prepared, TestStatus::LogTerminated], true)) {
                continue;
            }

            yield [$status];
        }
    }
}
