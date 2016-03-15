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

        $jsonParser = $this->prophesize('Paraunit\Parser\JSONLogParser');
        $jsonParser->getParsers()->willReturn(array($testResultContainer->reveal()));

        $formatter = new SingleResultFormatter($jsonParser->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals(sprintf('<%s>%s</%s>', $tag, $singleResult, $tag), $formattedResult);
    }

    public function testFormatSingleResultDoesNothingForUnknownTags()
    {
        $singleResult = '.';
        $jsonParser = $this->prophesize('Paraunit\Parser\JSONLogParser');
        $jsonParser->getParsers()->willReturn(array());

        $formatter = new SingleResultFormatter($jsonParser->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals($singleResult, $formattedResult);
    }
}
