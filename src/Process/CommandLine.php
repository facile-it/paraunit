<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Parser\JSON\TestHook as Hooks;

class CommandLine
{
    /** @var PHPUnitBinFile */
    protected $phpUnitBin;

    /** @var ChunkSize */
    protected $chunkSize;

    public function __construct(
        PHPUnitBinFile $phpUnitBin,
        ChunkSize $chunkSize
    ) {
        $this->phpUnitBin = $phpUnitBin;
        $this->chunkSize = $chunkSize;
    }

    /**
     * @return string[]
     */
    public function getExecutable(): array
    {
        return ['php', $this->phpUnitBin->getPhpUnitBin()];
    }

    /**
     * @param string[] $excludedPhpunitOptions
     *
     * @throws \RuntimeException When the config handling fails
     *
     * @return string[]
     */
    public function getOptions(PHPUnitConfig $config, array $excludedPhpunitOptions = []): array
    {
        $options = [];

        if (! $this->chunkSize->isChunked()) {
            $options[] = '--configuration=' . $config->getFileFullPath();
        }

        $options[] = '--extensions=' . implode(',', [
            Hooks\BeforeTest::class,
            Hooks\Error::class,
            Hooks\Failure::class,
            Hooks\Incomplete::class,
            Hooks\Risky::class,
            Hooks\Skipped::class,
            Hooks\Successful::class,
            Hooks\Warning::class,
        ]);

        foreach ($config->getPhpunitOptions() as $phpunitOption) {
            if (! in_array($phpunitOption->getName(), $excludedPhpunitOptions, true)) {
                $options[] = $this->buildPhpunitOptionString($phpunitOption);
            }
        }

        return $options;
    }

    private function buildPhpunitOptionString(PHPUnitOption $option): string
    {
        $optionString = '--' . $option->getName();
        if ($option->hasValue()) {
            $optionString .= '=' . $option->getValue();
        }

        return $optionString;
    }

    /**
     * @return string[]
     */
    public function getSpecificOptions(string $testFilename): array
    {
        return [];
    }
}
