<?php

namespace Paraunit\Proxy\Coverage;

use Paraunit\Configuration\OutputFile;

/**
 * Class CloverResult
 * @package Paraunit\Proxy\Coverage
 */
class CloverResult
{
    /** @var  \PHP_CodeCoverage_Report_Clover */
    private $clover;

    /**
     * CloverResult constructor.
     */
    public function __construct()
    {
        $this->clover = new \PHP_CodeCoverage_Report_Clover();
    }

    /**
     * @param \PHP_CodeCoverage $codeCoverage
     * @param OutputFile $targetFile
     */
    public function process(\PHP_CodeCoverage $codeCoverage, OutputFile $targetFile)
    {
        $this->clover->process($codeCoverage, $targetFile->getFilePath());
    }
}
