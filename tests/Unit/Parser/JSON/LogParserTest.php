<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Parser\JSON\Log;
use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\Parser\JSON\LogParser;
use Paraunit\Parser\JSON\ParserChainElementInterface;
use Paraunit\Parser\JSON\RetryParser;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
                $this->createLog(Log::STATUS_TEST_START),
                $this->createLog(Log::STATUS_SUCCESSFUL),
                $this->createLog(LogFetcher::LOG_ENDING_STATUS),
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
                $this->createLog(LogFetcher::LOG_ENDING_STATUS),
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
                $this->createLog(LogFetcher::LOG_ENDING_STATUS),
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
                $this->createLog(Log::STATUS_TEST_START),
                $this->createLog(Log::STATUS_ERROR),
                $this->createLog(LogFetcher::LOG_ENDING_STATUS),
            ]),
            $this->mockNoTestExecutedContainer(false),
            $this->mockEventDispatcher(ProcessToBeRetried::class),
            $this->mockRetryParser(true)
        );

        $parser->addParser($parser1->reveal());

        $parser->onProcessTerminated(new ProcessTerminated($process));
    }

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
        $noTestExecutedContainer->addProcessToFilenames(Argument::type(AbstractParaunitProcess::class))
            ->shouldBeCalledTimes((int) $noTestExecuted);

        return $noTestExecutedContainer->reveal();
    }

    /**
     * @param class-string<AbstractProcessEvent>|null $eventToBeDispatched
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

    private function createLog(string $status): Log
    {
        return new Log($status, 'testSomething', null);
    }
}
