<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Html\Facade;

class Html implements CoverageProcessorInterface
{
    private readonly Facade $html;

    public function __construct(private readonly OutputPath $targetPath)
    {
        $this->html = new Facade();
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $this->html->process($codeCoverage, $this->targetPath->getPath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'html';
    }
}
