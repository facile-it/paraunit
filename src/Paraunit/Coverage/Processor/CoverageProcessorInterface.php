<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use SebastianBergmann\CodeCoverage\CodeCoverage;

interface CoverageProcessorInterface
{
    /**
     * @param CodeCoverage $codeCoverage
     *
     * @throws \RuntimeException If the processor is missing a needed info, like the target dir/filename
     *
     * @return void
     */
    public function process(CodeCoverage $codeCoverage);

    public static function getConsoleOptionName(): string;
}
