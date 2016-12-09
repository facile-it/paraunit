<?php


namespace Paraunit\TestResult;

/**
 * Class TestResultWithSymbolFormat
 * @package Paraunit\TestResult
 */
class TestResultWithSymbolFormat extends TestResultFormat
{
    /** @var string */
    private $testResultSymbol;

    /**
     * TestResultFormat constructor.
     * @param string $testResultSymbol
     * @param string $tag
     * @param string $title
     * @param bool $printTestOutput
     * @param bool $printFilesRecap
     */
    public function __construct($testResultSymbol, $tag, $title, $printTestOutput = true, $printFilesRecap = true)
    {
        parent::__construct($tag, $title, $printTestOutput, $printFilesRecap);
        $this->testResultSymbol = $testResultSymbol;
    }

    /**
     * @return string
     */
    public function getTestResultSymbol()
    {
        return $this->testResultSymbol;
    }
}
