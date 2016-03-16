<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultContainer
 * @package Paraunit\TestResult
 */
class TestResultFormat
{
    /** @var string */
    private $testResultSymbol;

    /** @var string */
    private $tag;

    /** @var string */
    private $title;

    /** @var  bool */
    private $shouldPrintTestOutput;

    /** @var  bool */
    private $shouldPrintFilesRecap;

    /**
     * TestResultFormat constructor.
     * @param string $testResultSymbol
     * @param string $tag
     * @param string $title
     * @param bool $shouldPrintTestOutput
     * @param bool $shouldPrintFilesRecap
     */
    public function __construct($testResultSymbol, $tag, $title, $shouldPrintTestOutput = true, $shouldPrintFilesRecap = true)
    {
        $this->testResultSymbol = $testResultSymbol;
        $this->tag = $tag;
        $this->title = $title;
        $this->shouldPrintTestOutput = $shouldPrintTestOutput;
        $this->shouldPrintFilesRecap = $shouldPrintFilesRecap;
    }

    /**
     * @return string
     */
    public function getTestResultSymbol()
    {
        return $this->testResultSymbol;
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
        return $this->shouldPrintTestOutput;
    }

    /**
     * @return boolean
     */
    public function shouldPrintFilesRecap()
    {
        return $this->shouldPrintFilesRecap;
    }
}
