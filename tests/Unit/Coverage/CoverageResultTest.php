<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage;

use Paraunit\Coverage\CoverageMerger;
use Paraunit\Coverage\CoverageResult;
use Paraunit\Coverage\Processor\CoverageProcessorInterface;
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
        $merger = $this->prophesize(CoverageMerger::class);
        $merger->getCoverageData()
            ->willReturn($this->createCodeCoverage());

        $coverageResult = new CoverageResult($merger->reveal());

        $coverageResult->addCoverageProcessor($this->mockCoverageProcessorInterface());
        $coverageResult->addCoverageProcessor($this->mockCoverageProcessorInterface());
        $coverageResult->addCoverageProcessor($this->mockCoverageProcessorInterface());

        $coverageResult->generateResults();
    }

    private function mockCoverageProcessorInterface(): CoverageProcessorInterface
    {
        $coverageProcessor = $this->prophesize(CoverageProcessorInterface::class);
        $coverageProcessor->process(Argument::cetera())
            ->shouldBeCalledTimes(1);

        return $coverageProcessor->reveal();
    }
}
