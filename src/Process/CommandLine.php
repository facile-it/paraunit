<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Parser\JSON\TestHook as Hooks;
use Paraunit\Util\Log\JUnit\JUnit;

class CommandLine
{
    /** @var PHPUnitBinFile */
    protected $phpUnitBin;

    /** @var ChunkSize */
    protected $chunkSize;

    /** @var JUnit  */
    private $log;

    /** @var bool  */
    private $generateJunitLog = false;

    public function __construct(
        PHPUnitBinFile $phpUnitBin,
        ChunkSize $chunkSize,
        JUnit $log
    ) {
        $this->phpUnitBin = $phpUnitBin;
        $this->chunkSize = $chunkSize;
        $this->log = $log;
    }

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
            $options[] = $this->buildPhpunitOptionString($phpunitOption);
        }

        return $options;
    }

    private function buildPhpunitOptionString(PHPUnitOption $option): string
    {
        $optionString = '--' . $option->getName();

        // This is creating log-junit output -> merges multiple log-junit files into one report
        if ($option->getName() === 'log-junit' && $option->getValue() !== null) {
            $this->generateJunitLog = true;
            $this->log->setInputFileName($option->getValue());
        }

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
        $options = array();

        if ($this->generateJunitLog) {
            $options[] = $this->log->generateLogForTest($testFilename);
        }

        return $options;
    }
}
