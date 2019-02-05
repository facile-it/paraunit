<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class OutputFile
{
    /** @var string */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        if (is_string($filePath) && $filePath !== '') {
            $this->filePath = $filePath;
        }
    }

    public function isEmpty(): bool
    {
        return $this->filePath === null;
    }

    /**
     * @throws \RuntimeException
     */
    public function getFilePath(): string
    {
        if ($this->isEmpty()) {
            throw new \RuntimeException('Program requested an empty file path');
        }

        return $this->filePath;
    }
}
