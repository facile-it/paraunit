<?php

namespace Paraunit\Output;

use Paraunit\Process\ProcessResultInterface;

/**
 * Class NullOutputContainer
 * @package Paraunit\Output
 */
class NullOutputContainer extends AbstractOutputContainer implements OutputContainerInterface
{
    /**
     * {@inheritdoc}
     */
    public function addToOutputBuffer(ProcessResultInterface $process, $message)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFileNames()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputBuffer()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function countFiles()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function countMessages()
    {
        return 0;
    }
}
