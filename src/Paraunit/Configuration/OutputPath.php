<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class OutputPath
{
    /** @var string */
    private $path;

    /**
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
     * @throws \RuntimeException
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
