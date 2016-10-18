<?php

namespace Paraunit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\AbstractParaunitProcess;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\Process\Process;

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
     * @return CodeCoverage
     */
    public function fetch(AbstractParaunitProcess $process)
    {
        $tempFilename = $this->tempFilenameFactory->getFilenameForCoverage($process->getUniqueId());
        $codeCoverage = null;

        if ($this->coverageFileIsValid($tempFilename)) {
            $codeCoverage = require $tempFilename;
        }

        if ($codeCoverage instanceof CodeCoverage) {
            return $codeCoverage;
        }

        return new CodeCoverage();
    }

    /**
     * @param string $tempFilename
     * @return bool
     */
    private function coverageFileIsValid($tempFilename)
    {
        if (! file_exists($tempFilename)) {
            return false;
        }

        $verificationProcess = new Process('php --syntax-check ' . $tempFilename);
        $verificationProcess->start();
        $verificationProcess->wait();
        
        return $verificationProcess->getExitCode() == 0;
    }
}
