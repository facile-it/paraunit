<?php

namespace Paraunit\Proxy\Coverage;

use Paraunit\Configuration\OutputPath;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Html\Facade;

/**
 * Class HTMLResult
 * @package Paraunit\Proxy\Coverage
 */
class HtmlResult
{
    /** @var Facade */
    private $html;

    /**
     * CloverResult constructor.
     */
    public function __construct()
    {
        $this->html = new Facade();
    }

    /**
     * @param CodeCoverage $codeCoverage
     * @param OutputPath $targetPath
     */
    public function process(CodeCoverage $codeCoverage, OutputPath $targetPath)
    {
        $this->html->process($codeCoverage, $targetPath->getPath());
    }
}
