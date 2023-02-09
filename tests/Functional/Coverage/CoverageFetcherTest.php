<?php

namespace Tests\Functional\Coverage;

use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestIssue;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\StubbedParaunitProcess;

class CoverageFetcherTest extends BaseFunctionalTestCase
{
    protected function setup(): void
    {
        $this->configuration = new CoverageConfiguration(true);

        parent::setup();
    }

    public function testFetchMarksProcessWithMissingCoverageData(): void
    {
        $fetcher = $this->getService(CoverageFetcher::class);
        $process = new StubbedParaunitProcess();

        $fetcher->fetch($process);

        $resultContainer = $this->getService(TestResultContainer::class);
        $results = $resultContainer->getFileNames(TestIssue::CoverageFailure);
        $this->assertCount(1, $results);
        $this->assertContains($process->filename, $results);
    }
}
