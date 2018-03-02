<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use SebastianBergmann\CodeCoverage\CodeCoverage;

/***
 * Interface CoverageProcessorInterface
 * @package Paraunit\Proxy\Coverage
 */
interface CoverageProcessorInterface
{
    /**
     * @param CodeCoverage $codeCoverage
     * @return void
     * @throws \RuntimeException If the processor is missing a needed info, like the target dir/filename
     */
    public function process(CodeCoverage $codeCoverage);

    public static function getConsoleOptionName(): string;
}
