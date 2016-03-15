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
    public function __construct($filePath, $line)
    {
        $this->filePath = $filePath;
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->filePath . ':' . $this->line;
    }
}
