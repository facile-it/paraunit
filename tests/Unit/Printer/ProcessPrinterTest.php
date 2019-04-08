<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Printer\ProcessPrinter;
use Paraunit\Printer\SingleResultFormatter;
use Prophecy\Argument;
use Symfony\Component\Console\Output\Output;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;
use Tests\Stub\UnformattedOutputStub;

class ProcessPrinterTest extends BaseUnitTestCase
{
    public function testOnProcessParsingCompletedGoesToFormatting(): void
    {
        $testResult = $this->mockPrintableTestResult();
        $process = new StubbedParaunitProcess();
        $process->addTestResult($testResult);

        $formatter = $this->prophesize(SingleResultFormatter::class);
        $formatter->formatSingleResult($testResult)
            ->shouldBeCalled()
            ->willReturn('<ok>.</ok>');

        $output = new UnformattedOutputStub();
        $printer = new ProcessPrinter($formatter->reveal(), $output);

        $printer->onProcessCompleted(new ProcessEvent($process));

        $this->assertEquals('<ok>.</ok>', $output->getOutput());
    }

    public function testOnEngineEnd(): void
    {
        $formatter = $this->prophesize(SingleResultFormatter::class);
        $output = new UnformattedOutputStub();
        $printer = new ProcessPrinter($formatter->reveal(), $output);

        $printer->onEngineEnd();

        $consoleOutput = $output->getOutput();
        $this->assertEquals(ProcessPrinter::MAX_CHAR_LENGTH + 1, strlen($consoleOutput));
        $this->assertStringEndsWith(" 0\n", $consoleOutput);
    }

    /**
     * @dataProvider newLineTimesProvider
     */
    public function testOnProcessParsingCompletedAddsCounterAndNewlineAtFullRow(int $times, int $newLineTimes): void
    {
        $process = new StubbedParaunitProcess();
        for ($i = 0; $i < $times; ++$i) {
            $process->addTestResult($this->mockPrintableTestResult());
        }

        $output = $this->prophesize(Output::class);
        $output->write(Argument::any())
            ->shouldBeCalledTimes($times);
        $output->writeln(Argument::that(function ($input) {
            $this->assertEquals(6, strlen($input), 'Wrong output: ' . $input);

            return true;
        }))
            ->willReturn()
            ->shouldBeCalledTimes($newLineTimes);

        $formatter = $this->prophesize(SingleResultFormatter::class);
        $formatter->formatSingleResult(Argument::cetera())
            ->shouldBeCalledTimes($times)
            ->willReturn('<tag>.</tag>');

        $printer = new ProcessPrinter($formatter->reveal(), $output->reveal());

        $printer->onProcessCompleted(new ProcessEvent($process));
    }

    public function newLineTimesProvider(): array
    {
        return [
            [73, 0],
            [74, 0],
            [75, 1],
            [195, 2],
            [222, 2],
            [223, 3],
        ];
    }

    public function testOnProcessParsingCompletedAndOnEngineEndWorkWellTogether(): void
    {
        $process = new StubbedParaunitProcess();
        for ($i = 0; $i < 100; ++$i) {
            $process->addTestResult($this->mockPrintableTestResult());
        }

        $formatter = $this->prophesize(SingleResultFormatter::class);
        $formatter->formatSingleResult(Argument::cetera())
            ->shouldBeCalledTimes(100)
            ->willReturn('.');

        $output = new UnformattedOutputStub();
        $printer = new ProcessPrinter($formatter->reveal(), $output);

        $printer->onProcessCompleted(new ProcessEvent($process));
        $printer->onEngineEnd();

        $expectedOutput = str_repeat('.', 74) . '    74' . "\n"
            . str_repeat('.', 26)
            . str_repeat(' ', ProcessPrinter::MAX_CHAR_LENGTH - (26 + 3))
            . '100' . "\n";
        $this->assertSame($expectedOutput, $output->getOutput());
    }
}
