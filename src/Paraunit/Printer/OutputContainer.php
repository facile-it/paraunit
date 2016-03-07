<?php

namespace Paraunit\Printer;

/**
 * Class OutputContainer.
 */
class OutputContainer
{
    /**
     * @var string[]
     */
    protected $fileNames;

    /**
     * @var string[]
     */
    protected $outputBuffer;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $title;

    /**
     * @param string $tag
     * @param string $title
     */
    public function __construct($tag, $title)
    {
        $this->tag = $tag;
        $this->title = $title;
        $this->fileNames = array();
        $this->fileNames = array();
        $this->outputBuffer = array();
    }

    /**
     * @param string $fileName
     */
    public function addFileName($fileName)
    {
        $this->fileNames[] = $fileName;
    }

    /**
     * @param string|array $output
     */
    public function addToOutputBuffer($output)
    {
        if (is_array($output)) {
            foreach ($output as $single) {
                $this->outputBuffer[] = $single;
            }
        } else {
            $this->outputBuffer[] = $output;
        }
    }

    /**
     * @return \string[]
     */
    public function getFileNames()
    {
        return $this->fileNames;
    }

    /**
     * @return string[]
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
        return count($this->fileNames);
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
