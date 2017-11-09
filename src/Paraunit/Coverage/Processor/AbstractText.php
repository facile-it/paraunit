<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Proxy\Coverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Text as PHPUnitText;

abstract class AbstractText implements CoverageProcessorInterface
{
    /** @var PHPUnitText */
    private $text;

    /** @var bool */
    private $showColors;

    public function __construct(bool $showColors = false)
    {
        $this->text = new PHPUnitText(50, 90, false, false);
        $this->showColors = $showColors;
    }

    protected function getTextCoverage(CodeCoverage $codeCoverage): string
    {
        return $this->text->process($codeCoverage, $this->showColors);
    }
}
