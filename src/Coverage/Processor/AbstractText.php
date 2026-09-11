<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Facade;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractText implements CoverageProcessorInterface
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly bool $showColors,
        private readonly bool $onlySummary,
        private readonly ?OutputFile $targetFile = null,
    ) {}

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $coverageResults = Facade::fromObject($codeCoverage)->renderText(
            null,
            null,
            false,
            $this->onlySummary,
            $this->showColors,
        );

        if ($this->targetFile instanceof OutputFile) {
            file_put_contents($this->targetFile->getFilePath(), $coverageResults);
        } else {
            $this->output->writeln($coverageResults);
        }
    }
}
