<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

class TestResultFormat
{
    /** @var string */
    private $tag;

    /** @var string */
    private $title;

    /** @var bool */
    private $printTestOutput;

    /** @var bool */
    private $printFilesRecap;

    public function __construct(string $tag, string $title, bool $printTestOutput = true, bool $printFilesRecap = true)
    {
        $this->tag = $tag;
        $this->title = $title;
        $this->printTestOutput = $printTestOutput;
        $this->printFilesRecap = $printFilesRecap;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function shouldPrintTestOutput(): bool
    {
        return $this->printTestOutput;
    }

    public function shouldPrintFilesRecap(): bool
    {
        return $this->printFilesRecap;
    }
}
