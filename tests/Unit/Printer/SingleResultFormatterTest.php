<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Printer\SingleResultFormatter;
use Paraunit\TestResult\TestOutcomeContainer;
use Paraunit\TestResult\TestResultList;
use Paraunit\TestResult\TestResultWithSymbolFormat;
use Tests\BaseUnitTestCase;

class SingleResultFormatterTest extends BaseUnitTestCase
{
    public function testFormatSingleResult(): void
    {
        $tag = 'tag';
        $symbol = '.';
        $singleResult = $this->mockPrintableTestResult($symbol);
        $testResultContainer = $this->prophesize(TestOutcomeContainer::class);
        $testResultContainer->getTestResultFormat()
            ->willReturn(new TestResultWithSymbolFormat($symbol, $tag, 'title'));

        $testResultList = $this->prophesize(TestResultList::class);
        $testResultList->getTestResultContainers()
            ->willReturn([$testResultContainer->reveal()]);

        $formatter = new SingleResultFormatter($testResultList->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals(sprintf('<%s>%s</%s>', $tag, $symbol, $tag), $formattedResult);
    }

    public function testFormatSingleResultDoesNothingForUnknownTags(): void
    {
        $singleResult = $this->mockPrintableTestResult();
        $testResultList = $this->prophesize(TestResultList::class);
        $testResultList->getTestResultContainers()
            ->willReturn([]);

        $formatter = new SingleResultFormatter($testResultList->reveal());
        $formattedResult = $formatter->formatSingleResult($singleResult);

        $this->assertEquals('', $formattedResult);
    }
}
