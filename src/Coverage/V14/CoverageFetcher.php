<?php

declare(strict_types=1);

namespace Paraunit\Coverage\V14;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Process\Process;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestResult;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\Serialization\Unserializer;

class CoverageFetcher
{
    public function __construct(
        private readonly Unserializer $unserializer,
        private readonly TempFilenameFactory $tempFilenameFactory,
        private readonly TestResultContainer $testResultContainer,
    ) {}

    public function fetch(Process $process): ?ProcessedCodeCoverageData
    {
        $tempFilename = $this->tempFilenameFactory->getFilenameForCoverage($process->getUniqueId());

        try {
            return $this->unserializer->unserialize($tempFilename)['codeCoverage'];
        } catch (\Throwable) {
            $testResult = new TestResult(new Test($process->getFilename()), TestIssue::CoverageFailure);
            $this->testResultContainer->addTestResult($testResult);

            return new ProcessedCodeCoverageData();
        }
    }
}
