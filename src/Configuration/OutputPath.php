<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class OutputPath
{
    private readonly string $path;

    public function __construct(string $path)
    {
        if ($path === '') {
            throw new \InvalidArgumentException('Empty path provided: not valid');
        }

        $this->path = $path;
    }

    /**
     * @throws \RuntimeException
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
