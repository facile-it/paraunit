<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade;

class Xml implements CoverageProcessorInterface
{
    private readonly Facade $xml;

    public function __construct(
        private readonly OutputPath $targetPath,
    ) {
        $this->xml = new Facade();
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $this->xml->process(
            $this->targetPath->getPath(),
            $codeCoverage->getReport(),
            $codeCoverage->getTests(),
        );
    }

    public static function getConsoleOptionName(): string
    {
        return 'xml';
    }
}
