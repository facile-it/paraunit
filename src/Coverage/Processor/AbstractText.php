<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Text as PHPUnitText;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractText implements CoverageProcessorInterface
{
    private readonly PHPUnitText $text;

    public function __construct(
        private readonly OutputInterface $output,
        private readonly bool $showColors,
        bool $onlySummary,
        private readonly ?OutputFile $targetFile = null
    ) {
        $this->text = new PHPUnitText(Thresholds::default(), false, $onlySummary);
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $coverageResults = $this->text->process($codeCoverage, $this->showColors);

        if ($this->targetFile) {
            file_put_contents($this->targetFile->getFilePath(), $coverageResults);
        } else {
            $this->output->writeln($coverageResults);
        }
    }
}
