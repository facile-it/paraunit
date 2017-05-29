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

        $output = new UnformattedOutputStub();
        $printer = new ProcessPrinter($formatter->reveal(), $output);

        $printer->onProcessParsingCompleted(new ProcessEvent($process));

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

        $output = $this->prophesize(Output::class);
        $output->write(Argument::any())
            ->willReturn()
            ->shouldBeCalledTimes($times);
        $output->writeln('')
            ->willReturn()
            ->shouldBeCalledTimes($newLineTimes);

        $printer = new ProcessPrinter(
            $this->prophesize(SingleResultFormatter::class)->reveal(),
            $output->reveal()
        );

        $printer->onProcessParsingCompleted(new ProcessEvent($process));
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
