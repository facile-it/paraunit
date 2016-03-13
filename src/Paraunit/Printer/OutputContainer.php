<?php

namespace Paraunit\Printer;

use Paraunit\Process\ProcessResultInterface;

/**
 * Class OutputContainer.
 */
class OutputContainer extends AbstractOutputContainer implements OutputContainerInterface
{
    /** @var string[] */
    protected $outputBuffer;

    /**
     * @param ProcessResultInterface $process
     * @param string $message
     */
    public function addToOutputBuffer(ProcessResultInterface $process, $message)
    {
        $this->outputBuffer[$process->getFilename()][] = $message;
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
}
