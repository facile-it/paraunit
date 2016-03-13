<?php

namespace Paraunit\Printer;

/**
 * Class AbstractOutputContainer
 * @package Paraunit\Printer
 */
class AbstractOutputContainer
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
}
