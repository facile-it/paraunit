<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Proxy\Coverage\FakeDriver;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\Process\Process;

class CoverageFetcher
{
    public function __construct(
        private readonly TempFilenameFactory $tempFilenameFactory,
    ) {
    }

    public function fetch(AbstractParaunitProcess $process): CodeCoverage
    {
        $tempFilename = $this->tempFilenameFactory->getFilenameForCoverage($process->getUniqueId());
        $codeCoverage = null;

        if ($this->coverageFileIsValid($tempFilename)) {
            /** @psalm-suppress UnresolvableInclude */
            $codeCoverage = require $tempFilename;
            unlink($tempFilename);
        }

        if ($codeCoverage instanceof CodeCoverage) {
            return $codeCoverage;
        }

        // TODO - rework
//        $this->resultHandler->handleTestResult($process);

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
        } catch (\Exception) {
            return false;
        }
    }
}
