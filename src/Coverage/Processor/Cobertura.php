<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Facade;

class Cobertura implements CoverageProcessorInterface
{
    public function __construct(
        private readonly OutputFile $targetFile,
    ) {}

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        Facade::fromObject($codeCoverage)->renderCobertura($this->targetFile->getFilePath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'cobertura';
    }
}
