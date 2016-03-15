<?php

namespace Paraunit\Printer;

use Paraunit\Parser\JSONLogParser;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultContainerBearerInterface;
use Paraunit\TestResult\TestResultContainerInterface;
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
     * @param $singleResult
     * @return string
     */
    public function formatSingleResult($singleResult)
    {
        if (array_key_exists($singleResult, $this->tagMap)) {
            $tag = $this->tagMap[$singleResult];

            return sprintf('<%s>%s</%s>', $tag, $singleResult, $tag);
        }

        return $singleResult;
    }

    /**
     * @param TestResultFormat $format
     */
    private function addToMap(TestResultFormat $format)
    {
        $this->tagMap[$format->getTestResultSymbol()] = $format->getTag();
    }
}
