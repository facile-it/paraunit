<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class ChunkSize
{
    /** @var int */
    private $chunkSize;

    public function __construct(int $chunkSize)
    {
        if ($chunkSize < 1) {
            throw new \InvalidArgumentException('Chunk size has to be 1 or greater');
        }

        $this->chunkSize = $chunkSize;
    }

    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    public function isChunked(): bool
    {
        return $this->chunkSize > 1;
    }
}
