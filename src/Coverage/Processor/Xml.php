<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Facade;

class Xml implements CoverageProcessorInterface
{
    public function __construct(
        private readonly OutputPath $targetPath,
    ) {}

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        Facade::fromObject($codeCoverage)->renderXml($this->targetPath->getPath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'xml';
    }
}
