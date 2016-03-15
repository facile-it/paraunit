<?php

namespace Paraunit\Printer;

use Paraunit\Parser\JSONLogParser;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultContainerBearerInterface;
use Paraunit\TestResult\TestResultContainerInterface;

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
        $this->addToMap($logParser->getTestResultContainer());

        foreach ($logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof TestResultContainerBearerInterface) {
                $this->addToMap($parser->getTestResultContainer());
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
     * @param TestResultContainerInterface $container
     */
    private function addToMap(TestResultContainerInterface $container)
    {
        if ($container instanceof TestResultContainer) {
            $format = $container->getTestResultFormat();
            $this->tagMap[$format->getTestResultSymbol()] = $format->getTag();
        }
    }
}
