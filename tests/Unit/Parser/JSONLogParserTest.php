<?php

namespace Tests\Unit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Parser\JSONLogParser;
use Paraunit\TestResult\MuteTestResult;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultFormat;
use Paraunit\TestResult\TestResultWithMessage;
use Prophecy\Argument;
use Tests\Stub\StubbedParaProcess;

/**
 * Class JSONLogParserTest
 * @package Tests\Unit\Parser
 */
class JSONLogParserTest extends \PHPUnit_Framework_TestCase
{
    public function testOnProcessTerminated()
    {
        $process = new StubbedParaProcess();
        $process->setOutput('All ok');
        $parser1 = $this->prophesize('Paraunit\Parser\JSONParserChainElementInterface');
        $parser1->parseLog($process, Argument::cetera())->willReturn(null);
        $parser2 = $this->prophesize('Paraunit\Parser\JSONParserChainElementInterface');
        $parser2->parseLog($process, Argument::cetera())->willReturn(new MuteTestResult('.'));
        $parser3 = $this->prophesize('Paraunit\Parser\JSONParserChainElementInterface');
        $parser3->parseLog($process, Argument::cetera())->shouldNotBeCalled();
        $parser = $this->createParser(true, false);
        $parser->addParser($parser1->reveal());
        $parser->addParser($parser2->reveal());
        $parser->addParser($parser3->reveal());

        $parser->onProcessTerminated(new ProcessEvent($process));

        $this->assertFalse($process->hasAbnormalTermination());
        $this->assertEmpty($parser->getAbnormalTerminatedTestResultContainer()->getTestResults());
    }

    public function testOnProcessTerminatedHandlesLogNotParsed()
    {
        $process = new StubbedParaProcess();
        $process->setOutput('All ok');
        $parser1 = $this->prophesize('Paraunit\Parser\JSONParserChainElementInterface');
        $parser1->parseLog($process, Argument::cetera())->willReturn(null);
        $parser2 = $this->prophesize('Paraunit\Parser\JSONParserChainElementInterface');
        $parser2->parseLog($process, Argument::cetera())->willReturn(null);
        $parser = $this->createParser(true, false);
        $parser->addParser($parser1->reveal());
        $parser->addParser($parser2->reveal());

        $parser->onProcessTerminated(new ProcessEvent($process));

        $this->assertTrue($process->hasAbnormalTermination());
        $testResultContainer = $parser->getAbnormalTerminatedTestResultContainer();
        $this->assertNotEmpty($testResultContainer->getTestResults());
        $this->assertContainsOnlyInstancesOf('Paraunit\TestResult\TestResultWithMessage', $testResultContainer->getTestResults());
        $testResults = $testResultContainer->getTestResults(); // PHP 5.3 crap
        /** @var TestResultWithMessage $testResult */
        $testResult = array_pop($testResults); // PHP 5.3 crap, again
        $this->assertContains('testSomething', $testResult->getFunctionName());
        $this->assertContains($process->getOutput(), $testResult->getFailureMessage());
    }

    public function testParseHandlesMissingLogs()
    {
        $process = new StubbedParaProcess();
        $process->setOutput('Test output (core dumped)');
        $parser = $this->createParser(false);

        $parser->onProcessTerminated(new ProcessEvent($process));

        $this->assertTrue($process->hasAbnormalTermination());
        $testResultContainer = $parser->getAbnormalTerminatedTestResultContainer();
        $this->assertNotEmpty($testResultContainer->getTestResults());
        $this->assertContainsOnlyInstancesOf('Paraunit\TestResult\TestResultWithMessage', $testResultContainer->getTestResults());
        $testResults = $testResultContainer->getTestResults(); // PHP 5.3 crap
        /** @var TestResultWithMessage $testResult */
        $testResult = array_pop($testResults); // PHP 5.3 crap, again
        $this->assertContains('Unknown function -- test log not found', $testResult->getFunctionName());
        $this->assertContains($process->getOutput(), $testResult->getFailureMessage());
    }

    /**
     * @group this
     */
    public function testParseHandlesTruncatedLogs()
    {
        $process = new StubbedParaProcess();
        $process->setOutput('Test output (core dumped)');
        $parser = $this->createParser(true);

        $parser->onProcessTerminated(new ProcessEvent($process));

        $this->assertTrue($process->hasAbnormalTermination());
        $testResultContainer = $parser->getAbnormalTerminatedTestResultContainer();
        $this->assertNotEmpty($testResultContainer->getTestResults());
        $this->assertContainsOnlyInstancesOf('Paraunit\TestResult\TestResultWithMessage', $testResultContainer->getTestResults());
        $testResults = $testResultContainer->getTestResults(); // PHP 5.3 crap
        /** @var TestResultWithMessage $testResult */
        $testResult = array_pop($testResults); // PHP 5.3 crap, again
        $this->assertContains('testSomething', $testResult->getFunctionName());
        $this->assertContains($process->getOutput(), $testResult->getFailureMessage());
    }

    private function createParser($logFound = true, $abnormal = true)
    {
        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
        if ($logFound) {
            $log1 = new \stdClass();
            $log1->event = $abnormal ? 'testStart' : 'test';
            $log1->test = 'testSomething';
            $logLocator->fetch(Argument::cetera())->willReturn(array($log1));
        } else {
            $logLocator->fetch(Argument::cetera())->willThrow(new JSONLogNotFoundException(new StubbedParaProcess()));
        }

        return new JSONLogParser($logLocator->reveal(), new TestResultContainer(new TestResultFormat('', '', '')));
    }
}
