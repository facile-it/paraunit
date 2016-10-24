<?php

namespace Paraunit\Configuration;

/**
 * Class OutputFile
 * @package Paraunit\Configuration
 */
class OutputFile
{
    /** @var  string */
    private $filePath;

    /**
     * OutputPath constructor.
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        if (is_string($filePath) && $filePath !== '') {
            $this->filePath = $filePath;
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->filePath === null;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getFilePath()
    {
        if ($this->isEmpty()) {
            throw new \RuntimeException('Program requested an empty file path');
        }

        return $this->filePath;
    }
}
