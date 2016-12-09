<?php

namespace Paraunit\Printer;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\TestResultList;
use Paraunit\TestResult\TestResultWithSymbolFormat;

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
     * @param TestResultList $testResultList
     */
    public function __construct(TestResultList $testResultList)
    {
        $this->tagMap = array();

        foreach ($testResultList->getTestResultContainers() as $parser) {
            $format = $parser->getTestResultFormat();
            if ($format instanceof TestResultWithSymbolFormat) {
                $this->addToMap($format);
            }
        }
    }

    /**
     * @param PrintableTestResultInterface $singleResult
     * @return string
     */
    public function formatSingleResult(PrintableTestResultInterface $singleResult)
    {
        $format = $singleResult->getTestResultFormat();

        if (! $format instanceof TestResultWithSymbolFormat) {
            return '';
        }

        $resultSymbol = $format->getTestResultSymbol();
        $tag = $this->tagMap[$resultSymbol];

        return sprintf('<%s>%s</%s>', $tag, $resultSymbol, $tag);
    }

    /**
     * @param TestResultWithSymbolFormat $format
     */
    private function addToMap(TestResultWithSymbolFormat $format)
    {
        $this->tagMap[$format->getTestResultSymbol()] = $format->getTag();
    }
}
