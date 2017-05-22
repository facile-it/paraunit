<?php

namespace Paraunit\TestResult;

/**
 * Class TraceStep
 * @package Paraunit\TestResult
 */
class TraceStep
{
    /** @var string */
    private $filePath;

    /** @var int */
    private $line;

    /**
     * TraceStep constructor.
     * @param string $filePath
     * @param int $line
     */
    public function __construct(string $filePath, int $line)
    {
        $this->filePath = $filePath;
        $this->line = $line;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function __toString()
    {
        return $this->filePath . ':' . $this->line;
    }
}
