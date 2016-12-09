<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultContainer
 * @package Paraunit\TestResult
 */
class TestResultFormat
{
    /** @var string */
    private $tag;

    /** @var string */
    private $title;

    /** @var  bool */
    private $printTestOutput;

    /** @var  bool */
    private $printFilesRecap;

    /**
     * TestResultFormat constructor.
     * @param string $tag
     * @param string $title
     * @param bool $printTestOutput
     * @param bool $printFilesRecap
     */
    public function __construct($tag, $title, $printTestOutput = true, $printFilesRecap = true)
    {
        $this->tag = $tag;
        $this->title = $title;
        $this->printTestOutput = $printTestOutput;
        $this->printFilesRecap = $printFilesRecap;
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
     * @return boolean
     */
    public function shouldPrintTestOutput()
    {
        return $this->printTestOutput;
    }

    /**
     * @return boolean
     */
    public function shouldPrintFilesRecap()
    {
        return $this->printFilesRecap;
    }
}
