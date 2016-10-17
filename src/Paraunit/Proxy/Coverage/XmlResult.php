<?php

namespace Paraunit\Proxy\Coverage;

use Paraunit\Configuration\OutputPath;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade;

/**
 * Class XMLResult
 * @package Paraunit\Proxy\Coverage
 */
class XmlResult
{
    /** @var  Facade */
    private $xml;

    /**
     * CloverResult constructor.
     */
    public function __construct()
    {
        $this->xml = new Facade();
    }

    /**
     * @param CodeCoverage $codeCoverage
     * @param OutputPath $targetPath
     */
    public function process(CodeCoverage $codeCoverage, OutputPath $targetPath)
    {
        $this->xml->process($codeCoverage, $targetPath->getPath());
    }
}
