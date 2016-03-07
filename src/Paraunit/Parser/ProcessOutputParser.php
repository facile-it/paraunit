<?php

namespace Paraunit\Parser;

use Paraunit\Lifecycle\ProcessEvent;

/**
 * Class ProcessOutputParser.
 */
class ProcessOutputParser
{
    /** @var ProcessOutputParserChainElementInterface[] */
    protected $parsers;

    public function __construct()
    {
        $this->parsers = array();
    }

    /**
     * @param ProcessOutputParserChainElementInterface $parser
     */
    public function addParser(ProcessOutputParserChainElementInterface $parser)
    {
        $this->parsers[] = $parser;
    }

    /**
     * @return ProcessOutputParserChainElementInterface[]
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
            if ( ! $parser->parseAndContinue($processEvent->getProcess())) {
                return;
            }
        }
    }
}
