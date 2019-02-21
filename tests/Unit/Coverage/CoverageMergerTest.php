<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage;

use Paraunit\Coverage\CoverageFetcher;
use Paraunit\Coverage\CoverageMerger;
use Paraunit\Lifecycle\ProcessEvent;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class CoverageMergerTest extends BaseUnitTestCase
{
    public function testGetCoverageWhenNotReady(): void
    {
        $merger = new CoverageMerger($this->prophesize(CoverageFetcher::class)->reveal());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('not ready');

        $merger->getCoverageData();
    }

    public function testMergeFirstCoverageData(): void
    {
        $process = new StubbedParaunitProcess();

        $newCoverageData = $this->createCodeCoverage();

        $fetcher = $this->prophesize(CoverageFetcher::class);
        $fetcher->fetch($process)
            ->shouldBeCalledTimes(1)
            ->willReturn($newCoverageData);

        $merger = new CoverageMerger($fetcher->reveal());

        $merger->onProcessParsingCompleted(new ProcessEvent($process));

        $this->assertSame($newCoverageData, $merger->getCoverageData());
    }

    public function testMergeNextCoverageData(): void
    {
        $process1 = new StubbedParaunitProcess('test1');
        $process2 = new StubbedParaunitProcess('test2');

        $coverageData1 = $this->createCodeCoverage();
        $coverageData2 = $this->createCodeCoverage();
        $coverageData2->setTests([__CLASS__]);

        $fetcher = $this->prophesize(CoverageFetcher::class);
        $fetcher->fetch($process1)
            ->shouldBeCalledTimes(1)
            ->willReturn($coverageData1);
        $fetcher->fetch($process2)
            ->shouldBeCalledTimes(1)
            ->willReturn($coverageData2);

        $merger = new CoverageMerger($fetcher->reveal());

        $merger->onProcessParsingCompleted(new ProcessEvent($process1));

        $this->assertSame($coverageData1, $merger->getCoverageData());

        $merger->onProcessParsingCompleted(new ProcessEvent($process2));

        $this->assertSame($coverageData1, $merger->getCoverageData());
        $this->assertSame([__CLASS__], $merger->getCoverageData()->getTests());
    }
}
