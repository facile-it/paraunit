<?php

namespace Paraunit\Coverage\Processor;

use Paraunit\Proxy\Coverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Text;

/**
 * Class TextToConsole
 * @package Paraunit\Proxy\Coverage
 */
class TextToConsole implements CoverageProcessorInterface
{
    /** @var Text */
    protected $text;

    /** @var bool */
    protected $showColors;

    /**
     * TextToConsole constructor.
     */
    public function __construct()
    {
        $this->text = new Text(50, 90, false, false);
        $this->showColors = false;
    }

    /**
     * @param CodeCoverage $coverage
     */
    public function process(CodeCoverage $coverage)
    {
        $this->text->process($coverage, $this->showColors);
    }
}
