<?php

namespace Paraunit\Tests\Functional\Printer;

use Paraunit\Parser\JSONLogParser;
use Paraunit\Printer\SingleResultFormatter;
use Paraunit\Printer\SingleResultMarkerAwareInterface;
use Paraunit\Tests\BaseFunctionalTestCase;

/**
 * Class SingleResultFormatterTest
 * @package Paraunit\Tests\Functional\Printer
 */
class SingleResultFormatterTest extends BaseFunctionalTestCase
{
    public function testFormatSingleResult()
    {
        $singleResults = array('X', 'A');
        /** @var JSONLogParser $logParser */
        $logParser = $this->container->get('paraunit.parser.json_log_parser');
        foreach ($logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof SingleResultMarkerAwareInterface) {
                $singleResults[] = $parser->getSingleResultMarker();
            }
        }

        /** @var SingleResultFormatter $formatter */
        $formatter = $this->container->get('paraunit.printer.single_result_formatter');

        foreach ($singleResults as $singleResult) {
            $formattedResult = $formatter->formatSingleResult($singleResult);

            $this->assertNotEquals($singleResult, $formattedResult);
            $this->assertRegExp('/^<(\w+)>' . preg_quote($singleResult) . '<\/\1>$/', $formattedResult);
        }
    }
}
