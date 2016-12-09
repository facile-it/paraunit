<?php

namespace Tests\Unit\Parser;

use Paraunit\Parser\GenericParser;
use Paraunit\TestResult\FullTestResult;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class GenericParserTest
 * @package Tests\Unit\Parser
 */
class GenericParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider matchesProvider
     */
    public function testParsingFoundResult($statusToMatch, $startsWithToMatch, $status, $message, $shouldMatch = true)
    {
        $log = $this->getLogFromStub('test', $status, $message);
        if (null === $message) {
            unset($log->message);
        }

        $result = new FullTestResult('b', 'c');
        
        $factory = $this->prophesize('Paraunit\TestResult\TestResultFactory');
        $factory->createFromLog($log)->willReturn($result);
        $resultContainer = $this->prophesize('Paraunit\TestResult\TestResultContainer');
        $resultContainer->handleTestResult(Argument::cetera())
            ->shouldBeCalledTimes((int)$shouldMatch);

        $parser = new GenericParser($factory->reveal(), $resultContainer->reveal(), $statusToMatch, $startsWithToMatch);

        /** @var FullTestResult $result */
        $parsedResult = $parser->handleLogItem(new StubbedParaunitProcess(), $log);

        if ($shouldMatch) {
            $this->assertEquals($result, $parsedResult);
        } else {
            $this->assertNull($parsedResult);
        }
    }

    public function matchesProvider()
    {
        return array(
            array('error', null, 'error', 'anyMessage'),
            array('error', 'Error found', 'error', 'Error found'),

            array('error', null, 'pass', 'anyMessage', false),
            array('error', 'Error found', 'error', 'anoherMessage', false),
            array('error', 'Error found', 'error', null, false),
            array('error', 'Error found', 'pass', 'anoherMessage', false),
        );
    }
}
