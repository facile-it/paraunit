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
    protected $singleResultMarker;

    /** @var  string */
    protected $title;

    /** @var  string */
    protected $status;

    /** @var  string */
    protected $messageStartsWith;

    /**
     * AbstractParser constructor.
     *
     * @param OutputContainer $outputContainer
     * @param string $singleResultMarker The output of the single test result (.FERW etc)
     * @param string $status The status that the parser should catch
     * @param string | null $messageStartsWith The start of the message that the parser should look for
     */
    public function __construct(OutputContainer $outputContainer, $singleResultMarker, $status, $messageStartsWith = null)
    {
        $this->outputContainer = $outputContainer;
        $this->singleResultMarker = $singleResultMarker;
        $this->status = $status;
        $this->messageStartsWith = $messageStartsWith;
    }

    /**
     * {@inheritdoc}
     */
    public function parsingFoundResult(ProcessResultInterface $process, \stdClass $log)
    {
        if ($log->status == $this->status) {
            $process->addTestResult($this->singleResultMarker);
            $this->outputContainer->addToOutputBuffer($process, $log->message);

            return false;
        }

        return true;
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
