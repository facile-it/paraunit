<?php

namespace Paraunit\Tests\Unit\Printer;

use Paraunit\Printer\SingleResultFormatter;
use Paraunit\Tests\BaseUnitTestCase;

/**
 * Class SingleResultFormatterTest
 * @package Paraunit\Tests\Unit\Printer
 */
class SingleResultFormatterTest extends BaseUnitTestCase
{
    public function testFormatSingleResult()
    {
        $tag = 'tag';
        $singleResult = '.';
        $outputContainer = $this->prophesize('Paraunit\Printer\OutputContainerInterface');
        $outputContainer->getTag()->willReturn($tag);
        $outputContainer->getSingleResultMarker()->willReturn($singleResult);

        $parser = $this->prophesize('Paraunit\Parser\AbstractParser');
        $parser->getOutputContainer()->willReturn($outputContainer->reveal());

        $jsonParser = $this->prophesize('Paraunit\Parser\JSONLogParser');
        $jsonParser->getParsersForPrinting()->willReturn(array($parser->reveal(), $this->prophesize()->reveal()));
        $jsonParser->getOutputContainer()->willReturn($outputContainer->reveal());

        $formatter = new SingleResultFormatter($jsonParser->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals(sprintf('<%s>%s</%s>', $tag, $singleResult, $tag), $formattedResult);
    }

    public function testFormatSingleResultDoesNothingForUnknownTags()
    {
        $singleResult = '.';
        $outputContainer = $this->prophesize('Paraunit\Printer\OutputContainerInterface');
        $outputContainer->getTag()->willReturn('abnormal');
        $outputContainer->getSingleResultMarker()->willReturn('A');

        $jsonParser = $this->prophesize('Paraunit\Parser\JSONLogParser');
        $jsonParser->getParsersForPrinting()->willReturn(array());
        $jsonParser->getOutputContainer()->willReturn($outputContainer->reveal());

        $formatter = new SingleResultFormatter($jsonParser->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals($singleResult, $formattedResult);
    }
}
