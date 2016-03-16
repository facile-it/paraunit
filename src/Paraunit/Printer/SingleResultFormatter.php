<?php

namespace Paraunit\Printer;

use Paraunit\Parser\JSONLogParser;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultFormat;

/**
 * Class SingleResultFormatter
 * @package Paraunit\Printer
 */
class SingleResultFormatter
{
    /** @var  array */
    private $tagMap;

    /**
     * SingleResultFormatter constructor.
     * @param JSONLogParser $logParser
     */
    public function __construct(JSONLogParser $logParser)
    {
        $this->tagMap = array();

        foreach ($logParser->getParsers() as $parser) {
            if ($parser instanceof TestResultContainer) {
                $this->addToMap($parser->getTestResultFormat());
            }
        }
    }

    /**
     * @param PrintableTestResultInterface $singleResult
     * @return string
     */
    public function formatSingleResult(PrintableTestResultInterface $singleResult)
    {
        $resultSymbol = $singleResult->getTestResultFormat()->getTestResultSymbol();

        if (array_key_exists($resultSymbol, $this->tagMap)) {
            $tag = $this->tagMap[$resultSymbol];

            return sprintf('<%s>%s</%s>', $tag, $resultSymbol, $tag);
        }

        return $resultSymbol;
    }

    /**
     * @param TestResultFormat $format
     */
    private function addToMap(TestResultFormat $format)
    {
        $this->tagMap[$format->getTestResultSymbol()] = $format->getTag();
    }
}
