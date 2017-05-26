<?php
declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use Paraunit\Proxy\Coverage\CodeCoverage;
use PHPUnit\Runner\Version;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade;

/**
 * Class XMLResult
 * @package Paraunit\Proxy\Coverage
 */
class Xml implements CoverageProcessorInterface
{
    /** @var  Facade */
    private $xml;

    /** @var  OutputPath */
    private $targetPath;

    /**
     * Xml constructor.
     * @param OutputPath $targetPath
     */
    public function __construct(OutputPath $targetPath)
    {
        $this->xml = new Facade(Version::id());
        $this->targetPath = $targetPath;
    }

    /**
     * @param CodeCoverage $codeCoverage
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage)
    {
        $this->xml->process($codeCoverage, $this->targetPath->getPath());
    }
}
