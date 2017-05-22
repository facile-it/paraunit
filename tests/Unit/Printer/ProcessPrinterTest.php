<?php

namespace Tests\Unit\Printer;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Printer\ProcessPrinter;
use Paraunit\Printer\SingleResultFormatter;
use Prophecy\Argument;
use Symfony\Component\Console\Output\Output;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class ProcessPrinterTest
 * @package Tests\Unit\Printer
 */
class ProcessPrinterTest extends BaseUnitTestCase
{
    public function testPrintProcessGoesToFormatting()
    {
        $testResult = $this->mockPrintableTestResult();
        $process = new StubbedParaunitProcess();
        $process->addTestResult($testResult);

        $formatter = $this->prophesize(SingleResultFormatter::class);
        $formatter->formatSingleResult($testResult)
            ->shouldBeCalled()
            ->willReturn('<ok>.</ok>');

        $printer = new ProcessPrinter($formatter->reveal());
        $output = new UnformattedOutputStub();

        $processEvent = new ProcessEvent($process, ['output_interface' => $output]);
        $printer->onProcessTerminated($processEvent);

        $this->assertEquals('<ok>.</ok>', $output->getOutput());
    }

    /**
     * @dataProvider newLineTimesProvider
     */
    public function testPrintProcessResultAddsNewlineAfter80Chars(int $times, int $newLineTimes)
    {
        $process = new StubbedParaunitProcess();
        for ($i = 0; $i < $times; $i++) {
            $process->addTestResult($this->mockPrintableTestResult());
        }

        $formatter = $this->prophesize(SingleResultFormatter::class);
        $formatter->formatSingleResult(Argument::cetera())
            ->willReturn('<ok>.</ok>');
        $printer = new ProcessPrinter($formatter->reveal());
        $output = $this->prophesize(Output::class);
        $output->write(Argument::any())
            ->willReturn()
            ->shouldBeCalledTimes($times);
        $output->writeln('')
            ->willReturn()
            ->shouldBeCalledTimes($newLineTimes);

        $processEvent = new ProcessEvent($process, ['output_interface' => $output->reveal()]);
        $printer->onProcessTerminated($processEvent);
    }

    public function newLineTimesProvider(): array
    {
        return [
            [79, 0],
            [80, 0],
            [81, 1],
            [200, 2],
            [240, 2],
            [241, 3],
        ];
    }
}
