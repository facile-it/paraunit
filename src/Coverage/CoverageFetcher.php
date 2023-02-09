<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Process\Process;
use Paraunit\Proxy\Coverage\FakeDriver;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestResult;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\Process\Process as SymfonyProcess;

class CoverageFetcher
{
    public function __construct(
        private readonly TempFilenameFactory $tempFilenameFactory,
        private readonly TestResultContainer $testResultContainer,
    ) {
    }

    public function fetch(Process $process): CodeCoverage
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

        $testResult = new TestResult(new Test($process->getFilename()), TestIssue::CoverageFailure);
        $this->testResultContainer->addTestResult($testResult);

        return new CodeCoverage(new FakeDriver(), new Filter());
    }

    private function coverageFileIsValid(string $tempFilename): bool
    {
        if (! file_exists($tempFilename)) {
            return false;
        }

        try {
            $verificationProcess = new SymfonyProcess(['php', '--syntax-check', $tempFilename]);
            $verificationProcess->run();

            return $verificationProcess->getExitCode() === 0;
        } catch (\Exception) {
            return false;
        }
    }
}
