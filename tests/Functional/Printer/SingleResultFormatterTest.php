<?php

namespace Tests\Functional\Printer;

use Paraunit\Parser\JSONLogParser;
use Paraunit\Printer\SingleResultFormatter;
use Paraunit\Printer\SingleResultMarkerAwareInterface;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\TestResultBearerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultFormat;
use Tests\BaseFunctionalTestCase;

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
        /** @var JSONLogParser $logParser */
        $logParser = $this->container->get('paraunit.parser.json_log_parser');

        foreach ($logParser->getParsers() as $parser) {
            if ($parser instanceof TestResultContainer) {
                $this->assertMappingIsCorrect($formatter, $parser->getTestResultFormat());
            }
        }
    }

    private function assertMappingIsCorrect(SingleResultFormatter $formatter, TestResultFormat $testResultFormat)
    {
        $tag = $testResultFormat->getTag();
        $symbol = $testResultFormat->getTestResultSymbol();

        $testResult = $this->prophesize('Paraunit\TestResult\Interfaces\PrintableTestResultInterface');
        $testResult->getTestResultFormat()->willReturn($testResultFormat);

        $this->assertEquals(
            sprintf('<%s>%s</%s>', $tag, $symbol, $tag),
            $formatter->formatSingleResult($testResult->reveal()),
            'Mapping incorrect for test result symbol: ' . $symbol
        );
    }
}
