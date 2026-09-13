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
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\Exception as CodeCoverageException;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Serialization\Unserializer;

class CoverageFetcher
{
    private readonly Unserializer $unserializer;

    public function __construct(
        private readonly TempFilenameFactory $tempFilenameFactory,
        private readonly TestResultContainer $testResultContainer,
    ) {
        $this->unserializer = new Unserializer();
    }

    public function fetch(Process $process): CodeCoverage
    {
        $tempFilename = $this->tempFilenameFactory->getFilenameForCoverage($process->getUniqueId());

        try {
            $codeCoverageData = $this->unserializer->unserialize($tempFilename);

            // multiple coverage result could have different basePath
            $this->restoreAbsolutePaths($codeCoverageData['codeCoverage'], $codeCoverageData['basePath']);

            $codeCoverage = new CodeCoverage(new FakeDriver(), new Filter());
            $codeCoverage->setData($codeCoverageData['codeCoverage']);
            $codeCoverage->setTests($codeCoverageData['testResults']);

            return $codeCoverage;
        } catch (CodeCoverageException) {
            $testResult = new TestResult(new Test($process->getFilename()), TestIssue::CoverageFailure);
            $this->testResultContainer->addTestResult($testResult);

            return new CodeCoverage(new FakeDriver(), new Filter());
        } finally {
            if (file_exists($tempFilename)) {
                unlink($tempFilename);
            }
        }
    }

    private function restoreAbsolutePaths(ProcessedCodeCoverageData $data, string $basePath): void
    {
        if ($basePath === '') {
            return;
        }

        foreach ($data->coveredFiles() as $file) {
            $data->renameFile($file, $basePath . DIRECTORY_SEPARATOR . $file);
        }
    }
}
