<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\JSON;

use Paraunit\Parser\JSON\AbnormalTerminatedParser;
use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\TestResult\TestResultWithAbnormalTermination;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\StubbedParaunitProcess;

class AbnormalTerminatedParserTest extends BaseFunctionalTestCase
{
    public function testHandleLogItemWithAbnormalTermination(): void
    {
        $process = new StubbedParaunitProcess();
        $log = new \stdClass();
        $log->status = LogFetcher::LOG_ENDING_STATUS;
        $log->test = 'testFunction()';
        /** @var AbnormalTerminatedParser $parser */
        $parser = $this->getService(AbnormalTerminatedParser::class);

        $parsedResult = $parser->handleLogItem($process, $log);

        $this->assertInstanceOf(TestResultWithAbnormalTermination::class, $parsedResult);
    }

    /**
     * @dataProvider otherStatusesProvider
     */
    public function testHandleLogItemWithUncaughtLog(string $otherStatuses): void
    {
        $process = new StubbedParaunitProcess();
        $log = new \stdClass();
        $log->status = $otherStatuses;
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
            ['testStart'],
            ['suiteStart'],
            ['qwerty'],
            ['trollingYou'],
        ];
    }
}
