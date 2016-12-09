<?php

namespace Tests\Unit\Coverage;

use Paraunit\Coverage\CoverageFetcher;
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
    public function testFetch($coverageStub)
    {
        $process = new StubbedParaunitProcess('test1', 'uniqueId');

        $filename = $this->getTempFilename();
        copy($coverageStub, $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')->shouldBeCalled()->willReturn($filename);
        $missingCoverageContainer = $this->prophesize('Paraunit\TestResult\Interfaces\TestResultHandlerInterface');
        $missingCoverageContainer->addProcessToFilenames($process)
            ->shouldNotBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $missingCoverageContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf('Paraunit\Proxy\Coverage\CodeCoverage', $result);
        $this->assertNotEmpty($result->getData());
        $this->assertFileNotExists($filename, 'Coverage file should be deleted to preserve memory');
    }

    public function coverageStubProvider()
    {
        return array(
            array($this->getCoverageStubFilePath()),
            array($this->getCoverage4StubFilePath()),
        );
    }
    public function testFetchIgnoresMissingCoverageFiles()
    {
        $process = new StubbedParaunitProcess('test1', 'uniqueId');

        $tempFilenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')->shouldBeCalled()->willReturn('/path/to/missing/file');
        $missingCoverageContainer = $this->prophesize('Paraunit\TestResult\Interfaces\TestResultHandlerInterface');
        $missingCoverageContainer->addProcessToFilenames($process)
            ->shouldBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $missingCoverageContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf('Paraunit\Proxy\Coverage\CodeCoverage', $result);
        $this->assertEmpty($result->getData());
    }

    public function testFetchIgnoresWrongFiles()
    {
        $process = new StubbedParaunitProcess('test1', 'uniqueId');

        $filename = $this->getTempFilename();
        copy($this->getWrongCoverageStubFilePath(), $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')->shouldBeCalled()->willReturn($filename);
        $missingCoverageContainer = $this->prophesize('Paraunit\TestResult\Interfaces\TestResultHandlerInterface');
        $missingCoverageContainer->addProcessToFilenames($process)
            ->shouldBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $missingCoverageContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf('Paraunit\Proxy\Coverage\CodeCoverage', $result);
        $this->assertEmpty($result->getData());
        $this->assertFileNotExists($filename, 'Coverage file should be deleted to preserve memory');
    }

    /**
     * @return string
     */
    private function getTempFilename()
    {
        return uniqid(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testfile', true) . '.php';
    }
}
