<?php

declare(strict_types=1);

namespace Tests\Unit\Logs;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Logs\JSON\LogFetcher;
use Paraunit\Logs\JSON\LogParser;
use Paraunit\Logs\JSON\ParserChainElementInterface;
use Paraunit\Logs\JSON\RetryParser;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Process\Process;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class LogParserTest extends BaseUnitTestCase
{
    public function testOnProcessTerminatedHasProperChainInterruption(): void
    {
        $process = new StubbedParaunitProcess();
        $process->setOutput('All ok');

        $parser1 = $this->prophesize(ParserChainElementInterface::class);
        $parser1->handleLogItem($process, Argument::cetera())
            ->shouldBeCalledTimes(3)
            ->willReturn(null, null, null);
        $parser2 = $this->prophesize(ParserChainElementInterface::class);
        $parser2->handleLogItem($process, Argument::cetera())
            ->shouldBeCalledTimes(3)
            ->willReturn($this->mockTestResult(), $this->mockTestResult(), null);
        $parser3 = $this->prophesize(ParserChainElementInterface::class);
        $parser3->handleLogItem($process, Argument::cetera())
            ->shouldBeCalledOnce()
            ->willReturn($this->mockTestResult());

        $parser = new LogParser(
            $this->mockLogFetcher([
                $this->createLog(LogStatus::Prepared),
                $this->createLog(LogStatus::Passed),
                $this->createLog(LogStatus::LogTerminated),
            ]),
            $this->mockNoTestExecutedContainer(false),
            $this->mockEventDispatcher(ProcessParsingCompleted::class),
            $this->mockRetryParser(false)
        );

        $parser->addParser($parser1->reveal());
        $parser->addParser($parser2->reveal());
        $parser->addParser($parser3->reveal());

        $parser->onProcessTerminated(new ProcessTerminated($process));
    }

    public function testParseHandlesMissingLogs(): void
    {
        $process = new StubbedParaunitProcess();
        $process->setOutput('Test output (core dumped)');
        $process->setExitCode(139);
        $parser1 = $this->prophesize(ParserChainElementInterface::class);
        $parser1->handleLogItem($process, Argument::cetera())
            ->shouldBeCalledTimes(1)
            ->willReturn($this->mockTestResult());
        $parser2 = $this->prophesize(ParserChainElementInterface::class);
        $parser2->handleLogItem($process, Argument::cetera())
            ->shouldNotBeCalled();

        $parser = new LogParser(
            $this->mockLogFetcher([
                $this->createLog(LogStatus::LogTerminated),
            ]),
            $this->mockNoTestExecutedContainer(false),
            $this->mockEventDispatcher(ProcessParsingCompleted::class),
            $this->mockRetryParser(false)
        );

        $parser->addParser($parser1->reveal());
        $parser->addParser($parser2->reveal());

        $parser->onProcessTerminated(new ProcessTerminated($process));
    }

    public function testParseHandlesNoTestExecuted(): void
    {
        $process = new StubbedParaunitProcess();
        $process->setOutput('No tests executed!');
        $process->setExitCode(0);
        $parser1 = $this->prophesize(ParserChainElementInterface::class);
        $parser1->handleLogItem($process, Argument::cetera())
            ->shouldNotBeCalled();

        $parser = new LogParser(
            $this->mockLogFetcher([
                $this->createLog(LogStatus::LogTerminated),
            ]),
            $this->mockNoTestExecutedContainer(true),
            $this->mockEventDispatcher(),
            $this->mockRetryParser(false)
        );
        $parser->addParser($parser1->reveal());

        $parser->onProcessTerminated(new ProcessTerminated($process));
    }

    public function testParseHandlesTestToBeRetried(): void
    {
        $process = new StubbedParaunitProcess();
        $parser1 = $this->prophesize(ParserChainElementInterface::class);
        $parser1->handleLogItem($process, Argument::cetera())
            ->shouldNotBeCalled();

        $parser = new LogParser(
            $this->mockLogFetcher([
                $this->createLog(LogStatus::Prepared),
                $this->createLog(LogStatus::Errored),
                $this->createLog(LogStatus::LogTerminated),
            ]),
            $this->mockNoTestExecutedContainer(false),
            $this->mockEventDispatcher(ProcessToBeRetried::class),
            $this->mockRetryParser(true)
        );

        $parser->addParser($parser1->reveal());

        $parser->onProcessTerminated(new ProcessTerminated($process));
    }

    /**
     * @param LogData[] $logs
     */
    private function mockLogFetcher(array $logs): LogFetcher
    {
        $logLocator = $this->prophesize(LogFetcher::class);
        $logLocator->fetch(Argument::cetera())
            ->shouldBeCalledOnce()
            ->willReturn($logs);

        return $logLocator->reveal();
    }

    private function mockNoTestExecutedContainer(bool $noTestExecuted): TestResultHandlerInterface
    {
        $noTestExecutedContainer = $this->prophesize(TestResultHandlerInterface::class);
        $noTestExecutedContainer->addProcessToFilenames(Argument::type(Process::class))
            ->shouldBeCalledTimes((int) $noTestExecuted);

        return $noTestExecutedContainer->reveal();
    }

    /**
     * @param class-string|null $eventToBeDispatched
     */
    private function mockEventDispatcher(string $eventToBeDispatched = null): EventDispatcherInterface
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        if ($eventToBeDispatched) {
            $eventDispatcher->dispatch(Argument::type($eventToBeDispatched))
                ->shouldBeCalledOnce()
                ->willReturnArgument();
        } else {
            $eventDispatcher->dispatch(Argument::any())
                ->shouldNotBeCalled()
                ->willReturnArgument();
        }

        return $eventDispatcher->reveal();
    }

    private function mockRetryParser(bool $willBeRetried): RetryParser
    {
        $retryParser = $this->prophesize(RetryParser::class);
        $retryParser->processWillBeRetried(Argument::cetera())
            ->willReturn($willBeRetried);

        return $retryParser->reveal();
    }

    private function createLog(LogStatus $status): LogData
    {
        return new LogData($status, new Test('testSomething'), null);
    }
}
