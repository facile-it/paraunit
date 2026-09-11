<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Facade;

class Html implements CoverageProcessorInterface
{
    public function __construct(
        private readonly OutputPath $targetPath,
    ) {}

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        Facade::fromObject($codeCoverage)->renderHtml($this->targetPath->getPath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'html';
    }
}
