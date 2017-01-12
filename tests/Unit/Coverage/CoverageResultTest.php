<?php

namespace Tests\Unit\Coverage;

use Paraunit\Coverage\CoverageResult;
use Paraunit\Coverage\Processor\CoverageProcessorInterface;
use Paraunit\Proxy\Coverage\CodeCoverage;
use Prophecy\Argument;
use Tests\BaseTestCase;

/**
 * Class CoverageResultTest
 * @package Tests\Unit\Coverage
 */
class CoverageResultTest extends BaseTestCase
{
    public function testGenerateResults()
    {
        $merger = $this->prophesize('Paraunit\Coverage\CoverageMerger');
        $merger->getCoverageData()
            ->willReturn(new CodeCoverage());
        
        $coverageResult = new CoverageResult($merger->reveal());
        
        $coverageResult->addCoverageProcessor($this->mockCoverageProcessorInterface());
        $coverageResult->addCoverageProcessor($this->mockCoverageProcessorInterface());
        $coverageResult->addCoverageProcessor($this->mockCoverageProcessorInterface());

        $coverageResult->generateResults();
    }

    /**
     * @return CoverageProcessorInterface
     */
    private function mockCoverageProcessorInterface()
    {
        $coverageProcessor = $this->prophesize('Paraunit\Coverage\Processor\CoverageProcessorInterface');
        $coverageProcessor->process(Argument::cetera())
            ->shouldBeCalledTimes(1);
        
        return $coverageProcessor->reveal();
    }
}
