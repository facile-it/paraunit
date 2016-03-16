<?php

namespace Tests\Unit\Printer;

use Paraunit\Printer\SingleResultFormatter;
use Paraunit\TestResult\TestResultFormat;
use Tests\BaseUnitTestCase;

/**
 * Class SingleResultFormatterTest
 * @package Tests\Unit\Printer
 */
class SingleResultFormatterTest extends BaseUnitTestCase
{
    public function testFormatSingleResult()
    {
        $tag = 'tag';
        $symbol = '.';
        $singleResult = $this->mockPrintableTestResult($symbol);
        $testResultContainer = $this->prophesize('Paraunit\TestResult\TestResultContainer');
        $testResultContainer->getTestResultFormat()->willReturn(new TestResultFormat($symbol, $tag, 'title'));

        $jsonParser = $this->prophesize('Paraunit\Parser\JSONLogParser');
        $jsonParser->getParsers()->willReturn(array($testResultContainer->reveal()));

        $formatter = new SingleResultFormatter($jsonParser->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals(sprintf('<%s>%s</%s>', $tag, $symbol, $tag), $formattedResult);
    }

    public function testFormatSingleResultDoesNothingForUnknownTags()
    {
        $symbol = '.';
        $singleResult = $this->mockPrintableTestResult($symbol);
        $jsonParser = $this->prophesize('Paraunit\Parser\JSONLogParser');
        $jsonParser->getParsers()->willReturn(array());

        $formatter = new SingleResultFormatter($jsonParser->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals($symbol, $formattedResult);
    }
}
