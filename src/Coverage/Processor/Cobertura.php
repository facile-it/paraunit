<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Cobertura as PHPUnitCobertura;

class Cobertura implements CoverageProcessorInterface
{
    private readonly PHPUnitCobertura $cobertura;

    public function __construct(private readonly OutputFile $targetFile)
    {
        $this->cobertura = new PHPUnitCobertura();
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $this->cobertura->process($codeCoverage, $this->targetFile->getFilePath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'cobertura';
    }
}
