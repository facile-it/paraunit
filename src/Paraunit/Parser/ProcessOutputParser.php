<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

/**
 * Class ProcessOutputParser.
 */
class ProcessOutputParser
{
    /**
     * @var ProcessOutputParserChainElementInterface[]
     */
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
     * @param ProcessResultInterface $process
     */
    public function evaluateAndSetProcessResult(ProcessResultInterface $process)
    {
        foreach ($this->parsers as $parser) {
            if (!$parser->parseAndContinue($process)) {
                return;
            }
        }
    }
}
