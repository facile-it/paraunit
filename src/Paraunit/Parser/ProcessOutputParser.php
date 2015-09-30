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

    public function addParser(ProcessOutputParserChainElementInterface $parser)
    {
        $this->parsers[] = $parser;
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
    {
        foreach ($this->parsers as $parser) {
            if (!$parser->parseAndContinue($processEvent->getProcess())) {
                return;
            }
        }
    }
}
