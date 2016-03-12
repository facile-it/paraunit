<?php

namespace Paraunit\Parser;

use Paraunit\Printer\OutputContainer;
use Paraunit\Process\ProcessResultInterface;

/**
 * Class AbstractParser
 * @package Paraunit\Parser
 */
class AbstractParser implements JSONParserChainElementInterface, OutputContainerBearerInterface
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
     * @return OutputContainer
     */
    public function getOutputContainer()
    {
        return $this->outputContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function parsingFoundResult(ProcessResultInterface $process, \stdClass $log)
    {
        if ($log->status != $this->status) {
            return false;
        }

        if ($this->checkMessageStart($log)) {
            $process->addTestResult($this->singleResultMarker);
            $this->outputContainer->addToOutputBuffer($process, $log->message);

            return true;
        }

        return false;
    }

    /**
     * @param \stdClass $log
     * @return bool
     */
    private function checkMessageStart(\stdClass $log)
    {
        if (is_null($this->messageStartsWith)) {
            return true;
        }

        if ( ! property_exists($log, 'message')) {
            return false;
        }

        return 0 === strpos($log->message, $this->messageStartsWith);
    }
}
