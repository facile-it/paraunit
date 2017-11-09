<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Proxy\Coverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\PHP as PHPUnitPhp;

/**
 * Class PhpResult
 * @package Paraunit\Proxy\Coverage
 */
class Php implements CoverageProcessorInterface
{
    /** @var PHPUnitPhp */
    private $php;

    /** @var OutputFile */
    private $targetFile;

    /**
     * Php constructor.
     * @param OutputFile $targetFile
     */
    public function __construct(OutputFile $targetFile)
    {
        $this->php = new PHPUnitPhp();
        $this->targetFile = $targetFile;
    }

    /**
     * @param CodeCoverage $codeCoverage
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage)
    {
        $this->php->process($codeCoverage, $this->targetFile->getFilePath());
    }
}
