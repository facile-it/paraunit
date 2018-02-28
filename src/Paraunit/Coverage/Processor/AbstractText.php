<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Proxy\Coverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Text as PHPUnitText;

abstract class AbstractText implements CoverageProcessorInterface
{
    /** @var PHPUnitText */
    private $text;

    /** @var OutputFile */
    private $targetFile;

    /** @var bool */
    private $showColors;

    public function __construct(OutputFile $targetFile, bool $showColors, bool $onlySummary)
    {
        $this->text = new PHPUnitText(50, 90, false, $onlySummary);
        $this->targetFile = $targetFile;
        $this->showColors = $showColors;
    }

    /**
     * @param CodeCoverage $coverage
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $coverage)
    {
        file_put_contents(
            $this->targetFile->getFilePath(),
            $this->text->process($coverage, $this->showColors)
        );
    }
}
