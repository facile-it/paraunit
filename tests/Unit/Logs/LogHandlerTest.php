<?php

declare(strict_types=1);

namespace Tests\Unit\Logs;

use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Logs\JSON\LogParser;
use Paraunit\Logs\ValueObject\LogStatus;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class LogHandlerTest extends BaseUnitTestCase
{
    public function testParseHandlesNoTestExecuted(): void
    {
        $this->markTestIncomplete();
        // TODO - rewrite, test migrated from LogParserTest
        $process = new StubbedParaunitProcess();
        $process->output = 'No tests executed!';
        $process->exitCode = 0;

        $parser = new LogParser(
            $this->mockLogFetcher([
                $this->createLog(LogStatus::Prepared),
                $this->createLog(LogStatus::LogTerminated),
            ]),
            $this->mockNoTestExecutedContainer(true),
            $this->mockEventDispatcher(),
            $this->mockRetryParser(false)
        );
        $parser->addParser($parser1->reveal());

        $parser->onProcessTerminated(new ProcessTerminated($process));
    }
}
