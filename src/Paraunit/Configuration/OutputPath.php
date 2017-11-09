<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

/**
 * Class OutputPath
 * @package Paraunit\Configuration
 */
class OutputPath
{
    /** @var string */
    private $path;

    /**
     * OutputPath constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        if ($path === '') {
            throw new \InvalidArgumentException('Empty path provided: not valid');
        }

        $this->path = $path;
    }

    public function isEmpty(): bool
    {
        return $this->path === null;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
