<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Configuration\ChunkSize;

class EqualChunker implements TestChunkerInterface
{
    public function chunk(array $tests, ChunkSize $chunkSize): iterable
    {
        return array_chunk($tests, $chunkSize->getChunkSize());
    }
}
