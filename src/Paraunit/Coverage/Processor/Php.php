<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\PHP as PHPUnitPhp;

class Php implements CoverageProcessorInterface
{
    /** @var PHPUnitPhp */
    private $php;

    /** @var OutputFile */
    private $targetFile;

    public function __construct(OutputFile $targetFile)
    {
        $this->php = new PHPUnitPhp();
        $this->targetFile = $targetFile;
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage)
    {
        $this->php->process($codeCoverage, $this->targetFile->getFilePath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'php';
    }
}
