<?php

namespace Paraunit\Output;

use Paraunit\Process\ProcessWithResultsInterface;

/**
 * Class OutputContainer
 * @package Paraunit\Output
 */
class OutputContainer extends AbstractOutputContainer implements OutputContainerInterface
{
    /** @var string[][] */
    protected $outputBuffer;

    /**
     * {@inheritdoc}
     */
    public function __construct($tag, $title, $singleResultMarker)
    {
        parent::__construct($tag, $title, $singleResultMarker);

        $this->outputBuffer = array();
    }

    /**
     * @param ProcessWithResultsInterface $process
     * @param string $message
     */
    public function addToOutputBuffer(ProcessWithResultsInterface $process, $message)
    {
        if ($this->isEmptyMessage($message)) {
            $this->addFileNameOnly($process);
        } else {
            $this->outputBuffer[$process->getFilename()][] = $message;
        }
    }

    /**
     * @param ProcessWithResultsInterface $process
     */
    private function addFileNameOnly(ProcessWithResultsInterface $process)
    {
        if ( ! array_key_exists($process->getFilename(), $this->outputBuffer)) {
            $this->outputBuffer[$process->getFilename()] = array();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFileNames()
    {
        return array_keys($this->outputBuffer);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputBuffer()
    {
        return $this->outputBuffer;
    }

    /**
     * {@inheritdoc}
     */
    public function countFiles()
    {
        return count($this->outputBuffer);
    }

    /**
     * {@inheritdoc}
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
     * @param string | null $message
     * @return bool
     */
    private function isEmptyMessage($message)
    {
        switch (mb_strtolower(trim($message))) {
            case null:
            case '':
            case 'skipped test:':
            case 'incomplete test:':
                return true;
            default:
                return false;

        }
    }
}
