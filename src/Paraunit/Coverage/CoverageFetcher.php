<?php

namespace Paraunit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\AbstractParaunitProcess;

/**
 * Class CoverageFetcher
 * @package Paraunit\Coverage
 */
class CoverageFetcher
{
    /** @var  TempFilenameFactory */
    private $tempFilenameFactory;

    /**
     * CoverageFetcher constructor.
     * @param TempFilenameFactory $tempFilenameFactory
     */
    public function __construct(TempFilenameFactory $tempFilenameFactory)
    {
        $this->tempFilenameFactory = $tempFilenameFactory;
    }

    /**
     * @param AbstractParaunitProcess $process
     * @return \PHP_CodeCoverage
     */
    public function fetch(AbstractParaunitProcess $process)
    {
        $tempFilename = $this->tempFilenameFactory->getFilenameForCoverage($process->getUniqueId());
        $codeCoverage = null;

        if (file_exists($tempFilename)) {
            $codeCoverage = require $tempFilename;
        }

        if ($codeCoverage instanceof \PHP_CodeCoverage) {
            return $codeCoverage;
        }

        return new \PHP_CodeCoverage();
    }
}
