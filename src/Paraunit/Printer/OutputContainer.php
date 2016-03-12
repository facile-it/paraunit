<?php

namespace Paraunit\Printer;

use Paraunit\Process\ProcessResultInterface;

/**
 * Class OutputContainer.
 */
class OutputContainer implements OutputContainerInterface
{
    /** @var string[] */
    protected $outputBuffer;

    /** @var string */
    protected $tag;

    /** @var string */
    protected $title;

    /**
     * @param string $tag
     * @param string $title
     */
    public function __construct($tag, $title)
    {
        $this->tag = $tag;
        $this->title = $title;
        $this->outputBuffer = array();
    }

    /**
     * @param ProcessResultInterface $process
     * @param string $message
     */
    public function addToOutputBuffer(ProcessResultInterface $process, $message)
    {
        $this->outputBuffer[$process->getFilename()][] = $message;
    }

    /**
     * @return string[]
     */
    public function getFileNames()
    {
        return array_keys($this->outputBuffer);
    }

    /**
     * @return string[][]
     */
    public function getOutputBuffer()
    {
        return $this->outputBuffer;
    }

    /**
     * @return int
     */
    public function countFiles()
    {
        return count($this->outputBuffer);
    }

    /**
     * @return int
     */
    public function countMessages()
    {
        $messageCount = 0;
        foreach ($this->outputBuffer as $fileName => $fileMessages) {
            $messageCount += count($fileMessages);
        }

        return $messageCount;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
