<?php

namespace Tests\Unit\Parser\JSON;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Parser\JSON\LogFetcher;
use Paraunit\Parser\JSON\LogParser;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class LogParserTest
 * @package Tests\Unit\Parser\JSON
 */
class LogParserTest extends BaseUnitTestCase
{
    public function testOnProcessTerminatedHasProperChainInterruption()
    {
        $process = new StubbedParaunitProcess();
        $process->setOutput('All ok');
        $parser1 = $this->prophesize('Paraunit\Parser\JSON\ParserChainElementInterface');
        $parser1->handleLogItem($process, Argument::cetera())->shouldBeCalledTimes(2)->willReturn(null);
        $parser2 = $this->prophesize('Paraunit\Parser\JSON\ParserChainElementInterface');
        $parser2->handleLogItem($process, Argument::cetera())->shouldBeCalledTimes(2)->willReturn($this->mockTestResult());
        $parser3 = $this->prophesize('Paraunit\Parser\JSON\ParserChainElementInterface');
        $parser3->handleLogItem($process, Argument::cetera())->shouldNotBeCalled();
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
        $parser1 = $this->prophesize('Paraunit\Parser\JSON\ParserChainElementInterface');
        $parser1->handleLogItem($process, Argument::cetera())->shouldBeCalledTimes(1)->willReturn($this->mockTestResult());
        $parser2 = $this->prophesize('Paraunit\Parser\JSON\ParserChainElementInterface');
        $parser2->handleLogItem($process, Argument::cetera())->shouldNotBeCalled();

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
        $parser1 = $this->prophesize('Paraunit\Parser\JSON\ParserChainElementInterface');
        $parser1->handleLogItem($process, Argument::cetera())->shouldNotBeCalled();

        $parser = $this->createParser(false, false, 1);
        $parser->addParser($parser1->reveal());

        $parser->onProcessTerminated(new ProcessEvent($process));
    }
    
    /**
     * @param bool $logFound
     * @param bool $abnormal
     * @param int $emptyTestsCount Number of processes with no test executed
     * @return LogParser
     */
    private function createParser($logFound = true, $abnormal = true, $emptyTestsCount = 0)
    {
        $logLocator = $this->prophesize('Paraunit\Parser\JSON\LogFetcher');
        $endLog = new \stdClass();
        $endLog->status = LogFetcher::LOG_ENDING_STATUS;
        if ($logFound) {
            $log1 = new \stdClass();
            $log1->event = $abnormal ? 'testStart' : 'test';
            $log1->test = 'testSomething';
            $logLocator->fetch(Argument::cetera())->willReturn(array($log1, $endLog));
        } else {
            $logLocator->fetch(Argument::cetera())->willReturn(array($endLog));
        }
        
        $noTestExecutedContainer = $this->prophesize('Paraunit\TestResult\Interfaces\TestResultHandlerInterface');
        $noTestExecutedContainer->addProcessToFilenames(Argument::any())
            ->shouldBeCalledTimes($emptyTestsCount);

        return new LogParser($logLocator->reveal(), $noTestExecutedContainer->reveal());
    }
}
