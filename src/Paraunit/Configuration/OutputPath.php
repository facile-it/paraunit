<?php

namespace Paraunit\Configuration;

/**
 * Class OutputPath
 * @package Paraunit\Configuration
 */
class OutputPath
{
    /** @var  string */
    private $path;

    /**
     * OutputPath constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        if (strlen($path)) {
            $this->path = $path;
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->path);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if ($this->isEmpty()) {
            throw new \RuntimeException('Program requested an empty path');
        }

        return $this->path;
    }
}
