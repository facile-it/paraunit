<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover as PHPUnitClover;

class Clover implements CoverageProcessorInterface
{
    private readonly PHPUnitClover $clover;

    public function __construct(private readonly OutputFile $targetFile)
    {
        $this->clover = new PHPUnitClover();
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $this->clover->process($codeCoverage, $this->targetFile->getFilePath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'clover';
    }
}
