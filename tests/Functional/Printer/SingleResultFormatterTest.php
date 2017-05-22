<?php

namespace Tests\Functional\Printer;

use Paraunit\Printer\SingleResultFormatter;
use Paraunit\TestResult\TestResultFormat;
use Paraunit\TestResult\TestResultList;
use Paraunit\TestResult\TestResultWithSymbolFormat;
use Tests\BaseFunctionalTestCase;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

/**
 * Class SingleResultFormatterTest
 * @package Tests\Functional\Printer
 */
class SingleResultFormatterTest extends BaseFunctionalTestCase
{
    public function testFormatProvider()
    {
        /** @var SingleResultFormatter $formatter */
        $formatter = $this->container->get('paraunit.printer.single_result_formatter');
        /** @var TestResultList $testResultList */
        $testResultList = $this->container->get('paraunit.test_result.test_result_list');

        foreach ($testResultList->getTestResultContainers() as $resultContainer) {
            $this->assertMappingIsCorrect($formatter, $resultContainer->getTestResultFormat());
        }
    }

    private function assertMappingIsCorrect(SingleResultFormatter $formatter, TestResultFormat $testResultFormat)
    {
        if (! $testResultFormat instanceof TestResultWithSymbolFormat) {
            return;
        }

        $tag = $testResultFormat->getTag();
        $symbol = $testResultFormat->getTestResultSymbol();

        $testResult = $this->prophesize(PrintableTestResultInterface::class);
        $testResult->getTestResultFormat()
            ->willReturn($testResultFormat);

        $this->assertEquals(
            sprintf('<%s>%s</%s>', $tag, $symbol, $tag),
            $formatter->formatSingleResult($testResult->reveal()),
            'Mapping incorrect for test result symbol: ' . ($symbol ?: 'N/A')
            . '[' . $testResultFormat->getTitle() . ']'
        );
    }
}
