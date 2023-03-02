<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestResult;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class CoverageFetcherTest extends BaseUnitTestCase
{
    public function testFetch(): void
    {
        $process = new StubbedParaunitProcess('test.php', 'uniqueId');

        $filename = $this->getTempFilename();
        copy($this->getCoverageStubFilePath(), $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')
            ->shouldBeCalled()
            ->willReturn($filename);
        $testResultContainer = $this->prophesize(TestResultContainer::class);
        $testResultContainer->addTestResult(Argument::cetera())
            ->shouldNotBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $testResultContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertSame(['foo' => ['size' => '123', 'status' => 'bar']], $result->getTests());
        $this->assertFileDoesNotExist($filename, 'Coverage file should be deleted to preserve memory');
    }

    public function testFetchIgnoresMissingCoverageFiles(): void
    {
        $process = new StubbedParaunitProcess('test.php', 'uniqueId');

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')
            ->shouldBeCalled()
            ->willReturn('/path/to/missing/file');

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $this->mockTestResulContainer($process));

        $result = $fetcher->fetch($process);

        $this->assertEmpty($result->getTests());
    }

    public function testFetchIgnoresWrongFiles(): void
    {
        $process = new StubbedParaunitProcess('test.php', 'uniqueId');

        $filename = $this->getTempFilename();
        copy($this->getWrongCoverageStubFilePath(), $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getFilenameForCoverage($process->getUniqueId())
            ->shouldBeCalled()
            ->willReturn($filename);

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $this->mockTestResulContainer($process));

        $result = $fetcher->fetch($process);

        $this->assertEmpty($result->getTests());
        $this->assertFileDoesNotExist($filename, 'Coverage file should be deleted to preserve memory');
    }

    private function getTempFilename(): string
    {
        return uniqid(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testfile', true) . '.php';
    }

    private function mockTestResulContainer(StubbedParaunitProcess $process): TestResultContainer
    {
        $testResultContainer = $this->prophesize(TestResultContainer::class);
        $testResultContainer->addTestResult(Argument::that(function (TestResult $testResult) use ($process): bool {
            $this->assertSame(TestIssue::CoverageFailure, $testResult->status);
            $this->assertSame($process->filename, $testResult->test->name);

            return true;
        }))
            ->shouldBeCalled();

        return $testResultContainer->reveal();
    }
}
