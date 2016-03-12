<?php

namespace Paraunit\Parser;

use Paraunit\Printer\OutputContainer;
use Paraunit\Process\ProcessResultInterface;

/**
 * Class AbstractParser
 * @package Paraunit\Parser
 */
abstract class AbstractParser implements JSONParserChainElementInterface, OutputContainerBearerInterface
{
    /** @var  OutputContainer */
    protected $outputContainer;

    /** @var  string */
    protected $tag;

    /** @var  string */
    protected $title;

    /** @var  string */
    protected $messageStartsWith;

    /**
     * AbstractParser constructor.
     * @param string $tag
     * @param string $title
     * @param string | null $messageStartsWith The log result is distinguishable with the start of the message
     */
    public function __construct($tag, $title, $messageStartsWith = null)
    {
        $this->tag = $tag;
        $this->title = $title;
        $this->messageStartsWith = $messageStartsWith;

        $this->outputContainer = new OutputContainer($this->tag, $this->title);
    }

    /**
     * @return OutputContainer
     */
    public function getOutputContainer()
    {
        return $this->outputContainer;
    }

    /**
     * @param ProcessResultInterface $process
     * @return bool
     */
    protected function storeParsedBlocks(ProcessResultInterface $process)
    {
        $parsedBlob = $this->parse($process);

        if (isset($parsedBlob[1])) {
            $parsedBlocks = preg_split('/^\d+\) /m', $parsedBlob[1]);
            // il primo è sempre vuoto a causa dello split
            unset($parsedBlocks[0]);

            $this->outputContainer->addToOutputBuffer($parsedBlocks);
            $this->outputContainer->addFileName($process->getFilename());

            return true;
        }

        return false;
    }

    /**
     * @param ProcessResultInterface $process
     * @return bool
     */
    protected function parsingFoundSomething(ProcessResultInterface $process)
    {
        if (count($this->parse($process)) > 0) {
            $this->outputContainer->addFileName($process->getFilename());

            return true;
        }

        return false;
    }

    private function parse(ProcessResultInterface $process)
    {
        $parsedBlob = array();
        preg_match(static::PARSING_REGEX, $process->getOutput(), $parsedBlob);

        return $parsedBlob;
    }
}
