<?php

namespace Paraunit\Proxy\Coverage;

use SebastianBergmann\CodeCoverage\Report\Text;

/**
 * Class TextResult
 * @package Paraunit\Proxy\Coverage
 */
class TextResult
{
    /** @var  Text */
    private $text;

    /**
     * TextResult constructor.
     */
    public function __construct()
    {
        $this->text = new Text();
    }

    /**
     * @param CodeCoverage $coverage
     * @param bool $showColors
     * @return string The actual text coverage
     */
    public function process(CodeCoverage $coverage, $showColors = false)
    {
        return $this->text->process($coverage, $showColors);
    }
}
