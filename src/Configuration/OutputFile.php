<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class OutputFile
{
    private readonly string $filePath;

    public function __construct(string $filePath)
    {
        if ($filePath === '') {
            throw new \InvalidArgumentException('Empty path provided: not valid');
        }

        $this->filePath = $filePath;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
