<?php

namespace Tests\Unit\Printer;

use Paraunit\Printer\SingleResultFormatter;
use Paraunit\TestResult\TestResultWithSymbolFormat;
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
        $testResultContainer->getTestResultFormat()
            ->willReturn(new TestResultWithSymbolFormat($symbol, $tag, 'title'));

        $testResultList = $this->prophesize('Paraunit\TestResult\TestResultList');
        $testResultList->getTestResultContainers()
            ->willReturn(array($testResultContainer->reveal()));

        $formatter = new SingleResultFormatter($testResultList->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals(sprintf('<%s>%s</%s>', $tag, $symbol, $tag), $formattedResult);
    }

    public function testFormatSingleResultDoesNothingForUnknownTags()
    {
        $singleResult = $this->mockPrintableTestResult();
        $testResultList = $this->prophesize('Paraunit\TestResult\TestResultList');
        $testResultList->getTestResultContainers()->willReturn(array());

        $formatter = new SingleResultFormatter($testResultList->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals('', $formattedResult);
    }
}
