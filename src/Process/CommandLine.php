<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Parser\JSON\TestHook as Hooks;

class CommandLine
{
    /** @var PHPUnitBinFile */
    protected $phpUnitBin;

    public function __construct(PHPUnitBinFile $phpUnitBin)
    {
        $this->phpUnitBin = $phpUnitBin;
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
        $options = [
            '--configuration=' . $config->getFileFullPath(),
            '--extensions=' . implode(',', [
                Hooks\AfterLastTest::class,
                Hooks\BeforeTest::class,
                Hooks\Error::class,
                Hooks\Failure::class,
                Hooks\Incomplete::class,
                Hooks\Risky::class,
                Hooks\Skipped::class,
                Hooks\Successful::class,
                Hooks\Warning::class,
            ]),
        ];

        foreach ($config->getPhpunitOptions() as $phpunitOption) {
            $options[] = $this->buildPhpunitOptionString($phpunitOption);
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
