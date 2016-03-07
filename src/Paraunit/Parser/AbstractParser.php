<?php

namespace Paraunit\Parser;

use Paraunit\Printer\OutputContainer;
use Paraunit\Process\ProcessResultInterface;

/**
 * Class AbstractParser
 * @package Paraunit\Parser
 */
abstract class AbstractParser implements ProcessOutputParserChainElementInterface, OutputContainerBearerInterface
{
    /** @var  OutputContainer */
    protected $outputContainer;

    /**
     * AbstractParser constructor.
     */
    public function __construct()
    {
        $this->assertConstantExist('TAG');
        $this->assertConstantExist('TITLE');
        $this->assertConstantExist('PARSING_REGEX');

        $this->outputContainer = new OutputContainer(static::TAG, static::TITLE);
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

    /**
     * @param $constantName
     * @throws \Exception
     */
    private function assertConstantExist($constantName)
    {
        if ( ! defined('static::' . $constantName)) {
            throw new \Exception('Missing ' .$constantName . ' constant in ' . get_class($this));
        }
    }
}
