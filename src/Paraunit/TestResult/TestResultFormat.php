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

    /**
     * TestResultFormat constructor.
     * @param string $testResultSymbol
     * @param string $tag
     * @param string $title
     */
    public function __construct($testResultSymbol, $tag, $title)
    {
        $this->testResultSymbol = $testResultSymbol;
        $this->tag = $tag;
        $this->title = $title;
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
}
