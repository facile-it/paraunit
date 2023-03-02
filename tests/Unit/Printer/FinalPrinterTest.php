<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Lifecycle\TestCompleted;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Printer\FinalPrinter;
use Paraunit\TestResult\ValueObject\TestOutcome;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Stopwatch\Stopwatch;
use Tests\BaseUnitTestCase;
use Tests\Stub\UnformattedOutputStub;

class FinalPrinterTest extends BaseUnitTestCase
{
    #[DataProvider('chunkedDataProvider')]
    public function testOnEngineEndPrintsTheRightCountSummary(bool $isChunked, string $expectedTitle): void
    {
        ClockMock::register(Stopwatch::class);
        ClockMock::register(self::class);
        $output = new UnformattedOutputStub();

        $printer = new FinalPrinter($output, $this->mockChunkSize($isChunked));

        ClockMock::withClockMock(true);

        $printer->onEngineStart();
        $testCompleted = new TestCompleted(new Test('FooTest'), TestOutcome::Passed);
        $printer->onTestCompleted($testCompleted);
        $printer->onTestCompleted($testCompleted);
        $printer->onTestCompleted($testCompleted);
        $printer->onProcessParsingCompleted();
        $printer->onTestCompleted($testCompleted);
        $printer->onTestCompleted($testCompleted);
        $printer->onProcessToBeRetried();
        $printer->onProcessParsingCompleted();
        $printer->onTestCompleted($testCompleted);
        $printer->onProcessParsingCompleted();
        usleep(60_499_999);
        $printer->onEngineEnd();

        ClockMock::withClockMock(false);

        $this->assertStringContainsString('Execution time -- 00:01:00', $output->getOutput());
        $this->assertStringContainsString('Executed: 3 ' . $expectedTitle . ' (1 retried), 6 tests', $output->getOutput());
    }

    /**
     * @return array{bool, string}[]
     */
    public static function chunkedDataProvider(): array
    {
        return [
            [false, 'test classes'],
            [true, 'chunks'],
        ];
    }

    public function testOnEngineEndHandlesEmptyMessagesCorrectly(): void
    {
        $output = new UnformattedOutputStub();

        $printer = new FinalPrinter($output, $this->mockChunkSize(false));

        $printer->onEngineStart();
        $printer->onTestCompleted(new TestCompleted(new Test('FooTest'), TestOutcome::Failure));
        $printer->onProcessParsingCompleted();
        $printer->onEngineEnd();

        $this->assertStringNotContainsString('output', $output->getOutput());
    }

    private function mockChunkSize(bool $isChunked): ChunkSize
    {
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->shouldBeCalled()
            ->willReturn($isChunked);

        return $chunkSize->reveal();
    }
}
