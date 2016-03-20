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

        $tempFilenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')->shouldBeCalled()->willReturn($this->getCoverageStubFilePath());

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf('\PHP_CodeCoverage', $result);
        $this->assertNotEmpty($result->getData());
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

        $tempFilenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')->shouldBeCalled()->willReturn($this->getWrongCoverageStubFilePath());

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal());

        $result = $fetcher->fetch($process);

        $this->assertInstanceOf('\PHP_CodeCoverage', $result);
        $this->assertEmpty($result->getData());
    }
}
