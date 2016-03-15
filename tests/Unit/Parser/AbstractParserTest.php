<?php

namespace Tests\Unit\Parser;

use Paraunit\Parser\AbstractParser;
use Paraunit\TestResult\FullTestResult;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultFormat;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class AbstractParserTest
 * @package Tests\Unit\Parser
 */
class AbstractParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider matchesProvider
     */
    public function testParsingFoundResult($statusToMatch, $startsWithToMatch, $status, $message, $shouldMatch = true)
    {
        $log = $this->getLogWithStatus($status, $message);
        $result = new FullTestResult('a', 'b', 'c');
        $factory = $this->prophesize('Paraunit\TestResult\TestResultFactory');
        $factory->createFromLog($log)->willReturn($result);

        $parser = new AbstractParser($factory->reveal(), $statusToMatch, $startsWithToMatch);

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
            array('error', 'Error found', 'pass', 'anoherMessage', false),
        );
    }
}
