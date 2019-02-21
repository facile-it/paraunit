<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use SebastianBergmann\CodeCoverage\CodeCoverage;

interface CoverageProcessorInterface
{
    /**
     * @throws \RuntimeException If the processor is missing a needed info, like the target dir/filename
     */
    public function process(CodeCoverage $codeCoverage): void;

    public static function getConsoleOptionName(): string;
}
