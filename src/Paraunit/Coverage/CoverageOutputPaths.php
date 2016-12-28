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

    /** @var  OutputFile */
    private $textFile;

    /** @var  bool */
    private $textToConsole;

    /**
     * CoverageOutputPaths constructor.
     * @param OutputFile $cloverFilePath
     * @param OutputPath $xmlPath
     * @param OutputPath $htmlPath
     * @param OutputFile $textFile
     * @param bool $textToConsole
     */
    public function __construct(
        OutputFile $cloverFilePath,
        OutputPath $xmlPath,
        OutputPath $htmlPath,
        OutputFile $textFile,
        $textToConsole
    ) {
        $this->cloverFilePath = $cloverFilePath;
        $this->xmlPath = $xmlPath;
        $this->htmlPath = $htmlPath;
        $this->textFile = $textFile;
        $this->textToConsole = $textToConsole;
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

    /**
     * @return OutputFile
     */
    public function getTextFile()
    {
        return $this->textFile;
    }

    /**
     * @return bool
     */
    public function isTextToConsoleEnabled()
    {
        return $this->textToConsole;
    }
}
