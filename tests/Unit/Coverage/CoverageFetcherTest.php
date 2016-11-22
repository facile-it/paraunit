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
    public function setUp()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('Coverage driver not present in HHVM');
        }

        parent::setUp();
    }

    public function testFetch()
    {
        $process = new StubbedParaunitProcess('test1', 'uniqueId');

        $filename = uniqid('/tmp/testfile', true) . '.php';
        copy($this->getCoverageStubFilePath(), $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')->shouldBeCalled()->willReturn($filename);

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf('\PHP_CodeCoverage', $result);
        $this->assertNotEmpty($result->getData());
        $this->assertFileNotExists($filename, 'Coverage file should be deleted to preserve memory');
    }

    public function testFetchIgnoresMissingCoverageFiles()
    {
        $process = new StubbedParaunitProcess('test1', 'uniqueId');

        $tempFilenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')->shouldBeCalled()->willReturn('/path/to/missing/file');

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf('\PHP_CodeCoverage', $result);
        $this->assertEmpty($result->getData());
    }

    public function testFetchIgnoresWrongFiles()
    {
        $process = new StubbedParaunitProcess('test1', 'uniqueId');

        $filename = uniqid('/tmp/testfile', true) . '.php';
        copy($this->getWrongCoverageStubFilePath(), $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')->shouldBeCalled()->willReturn($filename);

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf('SebastianBergmann\CodeCoverage\CodeCoverage', $result);
        $this->assertEmpty($result->getData());
        $this->assertFileNotExists($filename, 'Coverage file should be deleted to preserve memory');
    }
}
