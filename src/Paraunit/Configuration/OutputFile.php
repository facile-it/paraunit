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
        if (strlen($filePath)) {
            $this->filePath = $filePath;
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->filePath);
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        if ($this->isEmpty()) {
            throw new \RuntimeException('Program requested an empty file path');
        }

        return $this->filePath;
    }
}
