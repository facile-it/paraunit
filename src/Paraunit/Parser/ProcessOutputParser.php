<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;
use Symfony\Component\Process\Process;


/**
 * Class ProcessOutputParser
 * @package Paraunit\Filter
 */
class ProcessOutputParser
{
    /**
     * @var ProcessOutputParserChainElementInterface[]
     */
    protected $parsers;

    function __construct()
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

        var_dump($process->getOutput());
        die();

        foreach($this->parsers as $parser) {
            if (!$parser->parseAndContinue($process)) {
                return;
            }
        }
    }
}
