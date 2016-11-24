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
        if (! extension_loaded('xdebug')) {
            $this->loadFakeXdebug();
        }
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
            unlink($tempFilename);
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

        try {
            $verificationProcess = new Process('php --syntax-check ' . $tempFilename);
            $verificationProcess->start();
            $verificationProcess->wait();

            return $verificationProcess->getExitCode() === 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * This function avoids exceptions when loading CodeCoverage instances when using PHPDBG and xdebug is missing.
     * Inspired by Symfony\Bridge\PhpUnit\ClockMock::register
     */
    private function loadFakeXdebug()
    {
        if (! function_exists('SebastianBergmann\Environment\extension_loaded')) {
            eval(<<<EOPHP
namespace SebastianBergmann\Environment;

function extension_loaded(\$extensionName)
{
    if (\$extensionName == 'xdebug') {
        return true;
    }
    
    return \\extension_loaded(\$extensionName);
}
EOPHP
            );
        }
    }
}
