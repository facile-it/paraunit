<?php

namespace Tests\Unit\Printer;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Printer\ProcessPrinter;
use Paraunit\TestResult\MuteTestResult;
use Tests\Stub\UnformattedOutputStub;
use Tests\Stub\StubbedParaunitProcess;
use Prophecy\Argument;

/**
 * Class ProcessPrinterTest
 * @package Tests\Unit\Printer
 */
class ProcessPrinterTest extends \PHPUnit_Framework_TestCase
{
    public function testPrintProcessGoesToFormatting()
    {
        $process = new StubbedParaunitProcess();
        $process->addTestResult(new MuteTestResult('.'));

        $formatter = $this->prophesize('Paraunit\Printer\SingleResultFormatter');
        $formatter->formatSingleResult('.')->shouldBeCalled()->willReturn('<ok>.</ok>');

        $printer = new ProcessPrinter($formatter->reveal());
        $output = new UnformattedOutputStub();

        $processEvent = new ProcessEvent($process, array('output_interface' => $output));
        $printer->onProcessTerminated($processEvent);

        $this->assertEquals('<ok>.</ok>', $output->getOutput());
    }

    /**
     * @dataProvider newLineTimesProvider
     */
    public function testPrintProcessResult_new_line_after_80_chars($times, $newLineTimes)
    {
        $process = new StubbedParaunitProcess();
        for ($i = 0; $i < $times; $i++) {
            $process->addTestResult(new MuteTestResult('F'));
        }

        $printer = new ProcessPrinter($this->prophesize('Paraunit\Printer\SingleResultFormatter')->reveal());
        $output = $this->prophesize('Symfony\Component\Console\Output\Output');
        $output->write(Argument::any())->willReturn()->shouldBeCalledTimes($times);
        $output->writeln('')->willReturn()->shouldBeCalledTimes($newLineTimes);

        $processEvent = new ProcessEvent($process, array('output_interface' => $output->reveal()));
        $printer->onProcessTerminated($processEvent);
    }

    public function newLineTimesProvider()
    {
        return array(
            array(79, 0),
            array(80, 0),
            array(81, 1),
            array(200, 2),
            array(240, 2),
            array(241, 3),
        );
    }
}
