<?php

namespace Paraunit\Output;

use Paraunit\Process\ProcessResultInterface;

/**
 * Interface OutputContainerInterface
 * @package Paraunit\Output
 */
interface OutputContainerInterface
{
    /**
     * @param ProcessResultInterface $process
     * @param string $message
     */
    public function addToOutputBuffer(ProcessResultInterface $process, $message);

    /**
     * @return string[]
     */
    public function getFileNames();

    /**
     * @return string[][]
     */
    public function getOutputBuffer();

    /**
     * @return int
     */
    public function countFiles();

    /**
     * @return int
     */
    public function countMessages();

   /**
     * @return string
     */
    public function getTag();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getSingleResultMarker();
}
