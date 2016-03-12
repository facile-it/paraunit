<?php

namespace Paraunit\Parser;

use Paraunit\Printer\OutputContainer;
use Paraunit\Process\ProcessResultInterface;

/**
 * Class IncompleteParser
 * @package Paraunit\Parser
 */
class IncompleteParser extends AbstractParser implements JSONParserChainElementInterface
{
    /**
     * @param OutputContainer $outputContainer
     */
    public function __construct(OutputContainer $outputContainer)
    {
        parent::__construct($outputContainer, 'incomplete', 'Incomplete Test: ');
    }

    /**
     * {@inheritdoc}
     */
    public function parsingFoundResult(ProcessResultInterface $process, \stdClass $log)
    {
        if ($log->status == $this->status) {
            $process->addTestResult('I');
            $this->outputContainer->addToOutputBuffer($process, $log->message);
        }

        return false;
    }
}
