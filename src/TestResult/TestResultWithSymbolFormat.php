<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

class TestResultWithSymbolFormat extends TestResultFormat
{
    public function __construct(
        private readonly string $testResultSymbol,
        string $tag,
        string $title,
        bool $printTestOutput = true,
        bool $printFilesRecap = true
    ) {
        parent::__construct($tag, $title, $printTestOutput, $printFilesRecap);
    }

    public function getTestResultSymbol(): string
    {
        return $this->testResultSymbol;
    }
}
