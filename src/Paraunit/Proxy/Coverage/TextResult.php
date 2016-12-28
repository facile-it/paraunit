<?php

namespace Paraunit\Proxy\Coverage;

use Paraunit\Configuration\OutputFile;
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
        $this->text = new Text(50, 90, false, false);
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

    /**
     * @param CodeCoverage $coverage
     * @param OutputFile $outputFile
     * @throws \RuntimeException
     */
    public function writeToFile(CodeCoverage $coverage, OutputFile $outputFile)
    {
        file_put_contents($outputFile->getFilePath(), $this->text->process($coverage, false));
    }
}
