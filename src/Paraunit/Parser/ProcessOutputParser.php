<?php

namespace Paraunit\Parser;

use Paraunit\Lifecycle\ProcessEvent;

/**
 * Class ProcessOutputParser.
 */
class ProcessOutputParser
{
    /** @var JSONParserChainElementInterface[] */
    protected $parsers;

    public function __construct()
    {
        $this->parsers = array();
    }

    /**
     * @param JSONParserChainElementInterface $parser
     */
    public function addParser(JSONParserChainElementInterface $parser)
    {
        $this->parsers[] = $parser;
    }

    /**
     * @return JSONParserChainElementInterface[]
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
    {
        foreach ($this->parsers as $parser) {
            if ( ! $parser->parsingFoundResult($processEvent->getProcess())) {
                return;
            }
        }
    }
}
