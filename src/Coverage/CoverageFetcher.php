<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Proxy\Coverage\FakeDriver;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
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

        return new CodeCoverage(new FakeDriver());
    }

    private function coverageFileIsValid(string $tempFilename): bool
    {
        if (! file_exists($tempFilename)) {
            return false;
        }

        try {
            $this->overrideCoverageClassDefinition($tempFilename);

            $verificationProcess = new Process(['php', '--syntax-check', $tempFilename]);
            $verificationProcess->run();

            return $verificationProcess->getExitCode() === 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function overrideCoverageClassDefinition(string $tempFilename): void
    {
        $originalCoverageData = file_get_contents($tempFilename);
        if ($originalCoverageData) {
            $fileContent = str_replace(
                [
                    'new SebastianBergmann\CodeCoverage\CodeCoverage;',
                    'new SebastianBergmann\CodeCoverage\CodeCoverage();',
                    'new CodeCoverage;',
                    'new CodeCoverage();',
                ],
                'new SebastianBergmann\CodeCoverage\CodeCoverage(new ' . FakeDriver::class . ');',
                $originalCoverageData
            );
        }

        file_put_contents($tempFilename, $fileContent ?? '');
    }
}
