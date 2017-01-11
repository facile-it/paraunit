<?php

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Proxy\Coverage\CodeCoverage;

/**
 * Class Text
 * @package Paraunit\Proxy\Coverage
 */
class Text extends TextToConsole implements CoverageProcessorInterface
{
    /** @var  OutputFile */
    private $targetFile;

    /**
     * Text constructor.
     * @param OutputFile $targetFile
     */
    public function __construct(OutputFile $targetFile)
    {
        parent::__construct();
        $this->targetFile = $targetFile;
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
