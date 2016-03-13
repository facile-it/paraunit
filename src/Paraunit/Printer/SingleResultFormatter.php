<?php

namespace Paraunit\Printer;

use Paraunit\Parser\JSONLogParser;
use Paraunit\Parser\OutputContainerBearerInterface;

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
        $this->addToMap($logParser);

        foreach ($logParser->getParsersForPrinting() as $parser) {
            $this->addToMap($parser);
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

    private function addToMap($parser)
    {
        if ($parser instanceof OutputContainerBearerInterface) {
            $container = $parser->getOutputContainer();
            $this->tagMap[$container->getSingleResultMarker()] = $container->getTag();
        }
    }
}
