<?php
declare(strict_types=1);

namespace Tests\Unit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\Proxy\Coverage\CodeCoverage;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class CoverageFetcherTest
 * @package Tests\Unit\Coverage
 */
class CoverageFetcherTest extends BaseUnitTestCase
{
    /**
     * @dataProvider coverageStubProvider
     */
    public function testFetch(string $coverageStub)
    {
        $process = new StubbedParaunitProcess('test.php', 'cmd', 'uniqueId');

        $filename = $this->getTempFilename();
        copy($coverageStub, $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')
            ->shouldBeCalled()
            ->willReturn($filename);
        $missingCoverageContainer = $this->prophesize(TestResultHandlerInterface::class);
        $missingCoverageContainer->addProcessToFilenames($process)
            ->shouldNotBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $missingCoverageContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf(CodeCoverage::class, $result);
        $this->assertNotEmpty($result->getData());
        $this->assertFileNotExists($filename, 'Coverage file should be deleted to preserve memory');
    }

    /**
     * @return string[]
     */
    public function coverageStubProvider(): array
    {
        return [
            [$this->getCoverageStubFilePath()],
            [$this->getCoverage4StubFilePath()],
        ];
    }
    public function testFetchIgnoresMissingCoverageFiles()
    {
        $process = new StubbedParaunitProcess('test.php', 'cmd', 'uniqueId');

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')
            ->shouldBeCalled()
            ->willReturn('/path/to/missing/file');
        $missingCoverageContainer = $this->prophesize(TestResultHandlerInterface::class);
        $missingCoverageContainer->addProcessToFilenames($process)
            ->shouldBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $missingCoverageContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf(CodeCoverage::class, $result);
        $this->assertEmpty($result->getData());
    }

    public function testFetchIgnoresWrongFiles()
    {
        $process = new StubbedParaunitProcess('test.php', 'cmd', 'uniqueId');

        $filename = $this->getTempFilename();
        copy($this->getWrongCoverageStubFilePath(), $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')
            ->shouldBeCalled()
            ->willReturn($filename);
        $missingCoverageContainer = $this->prophesize(TestResultHandlerInterface::class);
        $missingCoverageContainer->addProcessToFilenames($process)
            ->shouldBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $missingCoverageContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf(CodeCoverage::class, $result);
        $this->assertEmpty($result->getData());
        $this->assertFileNotExists($filename, 'Coverage file should be deleted to preserve memory');
    }

    private function getTempFilename(): string
    {
        return uniqid(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testfile', true) . '.php';
    }
}
