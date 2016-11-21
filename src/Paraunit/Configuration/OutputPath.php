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
        if (strlen($path) > 0) {
            $this->path = $path;
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->path === null;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getPath()
    {
        if ($this->isEmpty()) {
            throw new \RuntimeException('Program requested an empty path');
        }

        return $this->path;
    }
}
