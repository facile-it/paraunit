<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;

class CommandLine
{
    public function __construct(
        protected PHPUnitBinFile $phpUnitBin,
        protected ChunkSize $chunkSize
    ) {}

    /**
     * @return string[]
     */
    public function getExecutable(): array
    {
        return ['php', $this->phpUnitBin->getPhpUnitBin()];
    }

    /**
     * @throws \RuntimeException When the config handling fails
     *
     * @return string[]
     */
    public function getOptions(PHPUnitConfig $config): array
    {
        $options = [];

        if (! $this->chunkSize->isChunked()) {
            $options[] = '--configuration=' . $config->getFileFullPath();
        }

        return $options;
    }

    /**
     * @return string[]
     */
    public function getSpecificOptions(string $testFilename): array
    {
        return [];
    }
}
