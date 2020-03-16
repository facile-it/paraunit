<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\JSON;

use Paraunit\Parser\JSON\AbnormalTerminatedParser;
use Paraunit\Parser\JSON\Log;
use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\TestResult\NullTestResult;
use Paraunit\TestResult\TestResultWithAbnormalTermination;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\StubbedParaunitProcess;

class AbnormalTerminatedParserTest extends BaseFunctionalTestCase
{
    public function testHandleLogItemWithAbnormalTermination(): void
    {
        $process = new StubbedParaunitProcess();
        $process->setWaitingForTestResult(false);
        $logStart = new Log(Log::STATUS_TEST_START, __METHOD__, null);
        $logEnding = new Log(LogFetcher::LOG_ENDING_STATUS, __METHOD__, null);
        /** @var AbnormalTerminatedParser $parser */
        $parser = $this->getService(AbnormalTerminatedParser::class);

        $parsedResult = $parser->handleLogItem($process, $logStart);

        $this->assertInstanceOf(NullTestResult::class, $parsedResult);
        $this->assertTrue($process->isWaitingForTestResult());

        $parsedResult = $parser->handleLogItem($process, $logEnding);

        $this->assertInstanceOf(TestResultWithAbnormalTermination::class, $parsedResult);
        $this->assertFalse($process->isWaitingForTestResult());
    }

    /**
     * @dataProvider otherStatusesProvider
     */
    public function testHandleLogItemWithUncaughtLog(string $otherStatuses): void
    {
        $process = new StubbedParaunitProcess();
        $log = new Log($otherStatuses, __METHOD__, null);
        /** @var AbnormalTerminatedParser $parser */
        $parser = $this->getService(AbnormalTerminatedParser::class);

        $parsedResult = $parser->handleLogItem($process, $log);

        $this->assertNull($parsedResult);
        $this->assertFalse($process->hasAbnormalTermination());
    }

    /**
     * @return string[][]
     */
    public function otherStatusesProvider(): array
    {
        return [
            ['error'],
            ['fail'],
            ['pass'],
            ['suiteStart'],
            ['qwerty'],
            ['trollingYou'],
        ];
    }
}
