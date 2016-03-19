<?php

namespace Paraunit\Coverage;
use Paraunit\Configuration\OutputFile;
use Paraunit\Configuration\OutputPath;

/**
 * Class CoverageOutputPaths
 * @package Paraunit\Coverage
 */
class CoverageOutputPaths
{
    /** @var  OutputFile */
    private $cloverFilePath;

    /** @var  OutputPath */
    private $xmlPath;

    /** @var  OutputPath */
    private $htmlPath;

    /**
     * CoverageOutputPaths constructor.
     * @param OutputFile $cloverFilePath
     * @param OutputPath $xmlPath
     * @param OutputPath $htmlPath
     */
    public function __construct(OutputFile $cloverFilePath, OutputPath $xmlPath, OutputPath $htmlPath)
    {
        $this->cloverFilePath = $cloverFilePath;
        $this->xmlPath = $xmlPath;
        $this->htmlPath = $htmlPath;
    }

    /**
     * @return OutputFile
     */
    public function getCloverFilePath()
    {
        return $this->cloverFilePath;
    }

    /**
     * @return OutputPath
     */
    public function getXmlPath()
    {
        return $this->xmlPath;
    }

    /**
     * @return OutputPath
     */
    public function getHtmlPath()
    {
        return $this->htmlPath;
    }
}
