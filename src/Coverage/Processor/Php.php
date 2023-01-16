<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\PHP as PHPUnitPhp;

class Php implements CoverageProcessorInterface
{
    private readonly PHPUnitPhp $php;

    public function __construct(private readonly OutputFile $targetFile)
    {
        $this->php = new PHPUnitPhp();
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $this->php->process($codeCoverage, $this->targetFile->getFilePath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'php';
    }
}
