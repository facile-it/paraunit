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
        $singleResult = '.';
        $testResultContainer = $this->prophesize('Paraunit\TestResult\TestResultContainer');
        $testResultContainer->getTestResultFormat()->willReturn(new TestResultFormat($singleResult, $tag, 'title'));

        $parser = $this->prophesize('Paraunit\Parser\AbstractParser');
        $parser->getTestResultContainer()->willReturn($testResultContainer->reveal());

        $jsonParser = $this->prophesize('Paraunit\Parser\JSONLogParser');
        $jsonParser->getParsersForPrinting()->willReturn(array($parser->reveal(), $this->prophesize()->reveal()));
        $jsonParser->getTestResultContainer()->willReturn($testResultContainer->reveal());

        $formatter = new SingleResultFormatter($jsonParser->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals(sprintf('<%s>%s</%s>', $tag, $singleResult, $tag), $formattedResult);
    }

    public function testFormatSingleResultDoesNothingForUnknownTags()
    {
        $singleResult = '.';
        $testResultContainer = $this->prophesize('Paraunit\TestResult\TestResultContainer');
        $testResultContainer->getTestResultFormat()->willReturn(new TestResultFormat('abnormal', 'A', 'title'));

        $jsonParser = $this->prophesize('Paraunit\Parser\JSONLogParser');
        $jsonParser->getParsersForPrinting()->willReturn(array());
        $jsonParser->getTestResultContainer()->willReturn($testResultContainer->reveal());

        $formatter = new SingleResultFormatter($jsonParser->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals($singleResult, $formattedResult);
    }
}
