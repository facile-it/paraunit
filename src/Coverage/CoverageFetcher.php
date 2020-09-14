<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Proxy\Coverage\FakeDriver;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\Process\Process;

class CoverageFetcher
{
    /** @var TempFilenameFactory */
    private $tempFilenameFactory;

    /** @var TestResultHandlerInterface */
    private $resultHandler;

    public function __construct(TempFilenameFactory $tempFilenameFactory, TestResultHandlerInterface $failureHandler)
    {
        $this->tempFilenameFactory = $tempFilenameFactory;
        $this->resultHandler = $failureHandler;
    }

    public function fetch(AbstractParaunitProcess $process): CodeCoverage
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

        $this->resultHandler->addProcessToFilenames($process);

        return new CodeCoverage(new FakeDriver(), new Filter());
    }

    private function coverageFileIsValid(string $tempFilename): bool
    {
        if (! file_exists($tempFilename)) {
            return false;
        }

        try {
            $verificationProcess = new Process(['php', '--syntax-check', $tempFilename]);
            $verificationProcess->run();

            return $verificationProcess->getExitCode() === 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
