<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\Parser\JSON\LogParser;
use Paraunit\Parser\JSON\ParserChainElementInterface;
use Paraunit\Parser\JSON\RetryParser;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class LogParserTest
 */
class LogParserTest extends BaseUnitTestCase
{
    public function testOnProcessTerminatedHasProperChainInterruption()
    {
        $process = new StubbedParaunitProcess();
        $process->setOutput('All ok');
        $parser1 = $this->prophesize(ParserChainElementInterface::class);
        $parser1->handleLogItem($process, Argument::cetera())
            ->shouldBeCalledTimes(2)
            ->willReturn(null);
        $parser2 = $this->prophesize(ParserChainElementInterface::class);
        $parser2->handleLogItem($process, Argument::cetera())
            ->shouldBeCalledTimes(2)
            ->willReturn($this->mockTestResult());
        $parser3 = $this->prophesize(ParserChainElementInterface::class);
        $parser3->handleLogItem($process, Argument::cetera())
            ->shouldNotBeCalled();

        $parser = $this->createParser(true, false);
        $parser->addParser($parser1->reveal());
        $parser->addParser($parser2->reveal());
        $parser->addParser($parser3->reveal());

        $parser->onProcessTerminated(new ProcessEvent($process));
    }

    public function testParseHandlesMissingLogs()
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

        $parser = $this->createParser(false);
        $parser->addParser($parser1->reveal());
        $parser->addParser($parser2->reveal());

        $parser->onProcessTerminated(new ProcessEvent($process));
    }

    public function testParseHandlesNoTestExecuted()
    {
        $process = new StubbedParaunitProcess();
        $process->setOutput('No tests executed!');
        $process->setExitCode(0);
        $parser1 = $this->prophesize(ParserChainElementInterface::class);
        $parser1->handleLogItem($process, Argument::cetera())
            ->shouldNotBeCalled();

        $parser = $this->createParser(false, false, true);
        $parser->addParser($parser1->reveal());

        $parser->onProcessTerminated(new ProcessEvent($process));
    }

    public function testParseHandlesTestToBeRetried()
    {
        $process = new StubbedParaunitProcess();
        $process->setOutput('No tests executed!');
        $process->setExitCode(0);
        $parser1 = $this->prophesize(ParserChainElementInterface::class);
        $parser1->handleLogItem($process, Argument::cetera())
            ->shouldNotBeCalled();

        $parser = $this->createParser(true, false, false, true);
        $parser->addParser($parser1->reveal());

        $parser->onProcessTerminated(new ProcessEvent($process));
    }

    private function createParser(bool $logFound = true, bool $abnormal = true, bool $noTestExecuted = false, bool $willBeRetried = false): LogParser
    {
        $logLocator = $this->prophesize(LogFetcher::class);
        $endLog = new \stdClass();
        $endLog->status = LogFetcher::LOG_ENDING_STATUS;
        if ($logFound) {
            $log1 = new \stdClass();
            $log1->event = $abnormal ? 'testStart' : 'test';
            $log1->test = 'testSomething';
            $logLocator->fetch(Argument::cetera())
                ->willReturn([$log1, $endLog]);
        } else {
            $logLocator->fetch(Argument::cetera())
                ->willReturn([$endLog]);
        }

        $noTestExecutedContainer = $this->prophesize(TestResultHandlerInterface::class);
        $noTestExecutedContainer->addProcessToFilenames(Argument::any())
            ->shouldBeCalledTimes((int) $noTestExecuted);

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch('OtherEvents')
            ->shouldNotBeCalled();

        if ($willBeRetried) {
            $eventDispatcher->dispatch(
                ProcessEvent::PROCESS_TO_BE_RETRIED,
                Argument::type(ProcessEvent::class)
            )
                ->shouldBeCalledTimes(1);
        } elseif (! $noTestExecuted) {
            $eventDispatcher->dispatch(
                ProcessEvent::PROCESS_PARSING_COMPLETED,
                Argument::type(ProcessEvent::class)
            )
                ->shouldBeCalledTimes(1);
        }

        $retryParser = $this->prophesize(RetryParser::class);
        $retryParser->processWillBeRetried(Argument::cetera())
            ->shouldBeCalledTimes((int) ! $noTestExecuted)
            ->willReturn($willBeRetried);

        return new LogParser(
            $logLocator->reveal(),
            $noTestExecutedContainer->reveal(),
            $eventDispatcher->reveal(),
            $retryParser->reveal()
        );
    }
}
