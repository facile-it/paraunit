<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

class TestResultFormat
{
    public function __construct(private readonly string $tag, private readonly string $title, private readonly bool $printTestOutput = true, private readonly bool $printFilesRecap = true)
    {
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
