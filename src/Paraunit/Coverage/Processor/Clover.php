<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover as PHPUnitClover;

/**
 * Class Clover
 */
class Clover implements CoverageProcessorInterface
{
    /** @var PHPUnitClover */
    private $clover;

    /** @var OutputFile */
    private $targetFile;

    /**
     * Clover constructor.
     *
     * @param OutputFile $targetFile
     */
    public function __construct(OutputFile $targetFile)
    {
        $this->clover = new PHPUnitClover();
        $this->targetFile = $targetFile;
    }

    /**
     * @param CodeCoverage $codeCoverage
     *
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage)
    {
        $this->clover->process($codeCoverage, $this->targetFile->getFilePath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'clover';
    }
}
