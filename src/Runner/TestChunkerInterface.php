<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Configuration\ChunkSize;

interface TestChunkerInterface
{
    /**
     * Divides all tests into groups that will be run by a single process.
     *
     * @param string[] $tests
     *
     * @return iterable<int, string[]>
     */
    public function chunk(array $tests, ChunkSize $chunkSize): iterable;
}
