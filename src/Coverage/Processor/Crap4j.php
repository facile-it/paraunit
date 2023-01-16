<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Crap4j as PHPUnitCrap4j;

class Crap4j implements CoverageProcessorInterface
{
    private readonly PHPUnitCrap4j $crap4j;

    public function __construct(private readonly OutputFile $targetFile)
    {
        $this->crap4j = new PHPUnitCrap4j();
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $this->crap4j->process($codeCoverage, $this->targetFile->getFilePath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'crap4j';
    }
}
