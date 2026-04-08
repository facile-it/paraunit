<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use PHPUnit\Runner\Version;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Node\Directory;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade;

class Xml implements CoverageProcessorInterface
{
    private readonly Facade $xml;

    public function __construct(private readonly OutputPath $targetPath)
    {
        if (class_exists(Directory::class)) {
            $this->xml = new Facade();
        } else {
            $this->xml = new Facade(Version::id());
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $this->xml->process($codeCoverage, $this->targetPath->getPath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'xml';
    }
}
