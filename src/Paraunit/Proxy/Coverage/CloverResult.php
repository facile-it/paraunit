<?php

namespace Paraunit\Proxy\Coverage;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover;

/**
 * Class CloverResult
 * @package Paraunit\Proxy\Coverage
 *
 * TODO: refactor to a single class with injection
 */
class CloverResult
{
    /** @var Clover */
    private $clover;

    /**
     * CloverResult constructor.
     */
    public function __construct()
    {
        $this->clover = new Clover();
    }

    /**
     * @param CodeCoverage $codeCoverage
     * @param OutputFile $targetFile
     */
    public function process(CodeCoverage $codeCoverage, OutputFile $targetFile)
    {
        $this->clover->process($codeCoverage, $targetFile->getFilePath());
    }
}
