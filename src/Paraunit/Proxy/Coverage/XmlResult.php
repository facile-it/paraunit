<?php

namespace Paraunit\Proxy\Coverage;

use Paraunit\Configuration\OutputPath;

/**
 * Class XMLResult
 * @package Paraunit\Proxy\Coverage
 */
class XmlResult
{
    /** @var  \PHP_CodeCoverage_Report_XML */
    private $xml;

    /**
     * CloverResult constructor.
     */
    public function __construct()
    {
        $this->xml = new \PHP_CodeCoverage_Report_XML();
    }

    /**
     * @param \PHP_CodeCoverage $codeCoverage
     * @param OutputPath $targetPath
     */
    public function process(\PHP_CodeCoverage $codeCoverage, OutputPath $targetPath)
    {
        $this->xml->process($codeCoverage, $targetPath->getPath());
    }
}
