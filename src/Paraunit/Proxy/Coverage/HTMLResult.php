<?php

namespace Paraunit\Proxy\Coverage;

use Paraunit\Configuration\OutputPath;

/**
 * Class HTMLResult
 * @package Paraunit\Proxy\Coverage
 */
class HTMLResult
{
    /** @var  \PHP_CodeCoverage_Report_HTML */
    private $html;

    /**
     * CloverResult constructor.
     */
    public function __construct()
    {
        $this->html = new \PHP_CodeCoverage_Report_HTML();
    }

    /**
     * @param \PHP_CodeCoverage $codeCoverage
     * @param OutputPath $targetPath
     */
    public function process(\PHP_CodeCoverage $codeCoverage, OutputPath $targetPath)
    {
        $this->html->process($codeCoverage, $targetPath->getPath());
    }
}
