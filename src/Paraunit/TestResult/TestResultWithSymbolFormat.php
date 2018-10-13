<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

/**
 * Class TestResultWithSymbolFormat
 */
class TestResultWithSymbolFormat extends TestResultFormat
{
    /** @var string */
    private $testResultSymbol;

    /**
     * TestResultFormat constructor.
     *
     * @param string $testResultSymbol
     * @param string $tag
     * @param string $title
     * @param bool $printTestOutput
     * @param bool $printFilesRecap
     */
    public function __construct(
        string $testResultSymbol,
        string $tag,
        string $title,
        bool $printTestOutput = true,
        bool $printFilesRecap = true
    ) {
        parent::__construct($tag, $title, $printTestOutput, $printFilesRecap);
        $this->testResultSymbol = $testResultSymbol;
    }

    public function getTestResultSymbol(): string
    {
        return $this->testResultSymbol;
    }
}
