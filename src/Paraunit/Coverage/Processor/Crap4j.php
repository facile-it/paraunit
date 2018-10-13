<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Crap4j as PHPUnitCrap4j;

/**
 * Class Crap4jResult
 */
class Crap4j implements CoverageProcessorInterface
{
    /** @var PHPUnitCrap4j */
    private $crap4j;

    /** @var OutputFile */
    private $targetFile;

    /**
     * Crap4j constructor.
     *
     * @param OutputFile $targetFile
     */
    public function __construct(OutputFile $targetFile)
    {
        $this->crap4j = new PHPUnitCrap4j();
        $this->targetFile = $targetFile;
    }

    /**
     * @param CodeCoverage $codeCoverage
     *
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage)
    {
        $this->crap4j->process($codeCoverage, $this->targetFile->getFilePath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'crap4j';
    }
}
