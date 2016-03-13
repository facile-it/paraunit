<?php

namespace Paraunit\Printer;
use Paraunit\Process\ProcessResultInterface;

/**
 * Class AbstractOutputContainer
 * @package Paraunit\Printer
 */
abstract class AbstractOutputContainer implements OutputContainerInterface
{
    /** @var string */
    protected $singleResultMarker;

    /** @var string */
    protected $tag;

    /** @var string */
    protected $title;

    /**
     * OutputContainer constructor.
     * @param string $tag
     * @param string $title
     * @param string $singleResultMarker
     */
    public function __construct($tag, $title, $singleResultMarker)
    {
        $this->tag = $tag;
        $this->title = $title;
        $this->singleResultMarker = $singleResultMarker;
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

    /**
     * @return string
     */
    public function getSingleResultMarker()
    {
        return $this->singleResultMarker;
    }

    abstract public function addToOutputBuffer(ProcessResultInterface $process, $message);

    abstract public function getFileNames();

    abstract public function getOutputBuffer();

    abstract public function countFiles();
}
