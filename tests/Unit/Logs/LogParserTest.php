<?php

declare(strict_types=1);

namespace Tests\Unit\Logs;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Logs\JSON\LogFetcher;
use Paraunit\Logs\JSON\LogHandler;
use Paraunit\Logs\JSON\LogParser;
use Paraunit\Logs\JSON\RetryParser;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class LogParserTest extends BaseUnitTestCase
{
    public function testOnProcessTerminatedLogsAreHandled(): void
    {
        $process = new StubbedParaunitProcess();
        $process->output = 'All ok';

        $parser = new LogParser(
            $this->mockLogFetcher(),
            $this->mockLogHandler(),
            $this->mockRetryParser(false),
            $this->mockEventDispatcher(ProcessParsingCompleted::class),
        );

        $parser->onProcessTerminated(new ProcessTerminated($process));
    }

    public function testParseHandlesMissingLogs(): void
    {
        $process = new StubbedParaunitProcess();
        $process->output = 'Test output (core dumped)';
        $process->exitCode = 139;

        $logHandler = $this->prophesize(LogHandler::class);
        $logHandler->reset()
            ->shouldBeCalledOnce();
        $logHandler->processNoLogAvailable($process)
            ->shouldBeCalledOnce();

        $parser = new LogParser(
            $this->mockLogFetcher([]),
            $logHandler->reveal(),
            $this->mockRetryParser(false),
            $this->mockEventDispatcher(ProcessParsingCompleted::class),
        );

        $parser->onProcessTerminated(new ProcessTerminated($process));
    }

    public function testParseHandlesTestToBeRetried(): void
    {
        $process = new StubbedParaunitProcess();

        $parser = new LogParser(
            $this->mockLogFetcher([
                $this->createLog(LogStatus::Started),
                $this->createLog(LogStatus::Prepared),
                $this->createLog(LogStatus::Errored),
                $this->createLog(LogStatus::Finished),
                $this->createLog(LogStatus::LogTerminated),
            ]),
            $this->mockLogHandler(shouldBeCalled: false),
            $this->mockRetryParser(true),
            $this->mockEventDispatcher(ProcessToBeRetried::class),
        );

        $parser->onProcessTerminated(new ProcessTerminated($process));
    }

    /**
     * @param LogData[]|null $logs
     */
    private function mockLogFetcher(array $logs = null): LogFetcher
    {
        $logs ??= $this->createLogsForOnePassedTest();
        $logLocator = $this->prophesize(LogFetcher::class);
        $logLocator->fetch(Argument::cetera())
            ->shouldBeCalledOnce()
            ->willReturn($logs);

        return $logLocator->reveal();
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
                ->shouldNotBeCalled();
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

    private function mockLogHandler(bool $shouldBeCalled = true): LogHandler
    {
        $logHandler = $this->prophesize(LogHandler::class);

        $logHandler->reset()
            ->shouldBeCalledOnce()
            ->will(function () use ($logHandler, $shouldBeCalled): void {
                if ($shouldBeCalled) {
                    $logHandler->processLog(Argument::cetera())->shouldBeCalled();
                } else {
                    $logHandler->processLog(Argument::cetera())
                        ->shouldNotBeCalled();
                }
            });

        return $logHandler->reveal();
    }
}
