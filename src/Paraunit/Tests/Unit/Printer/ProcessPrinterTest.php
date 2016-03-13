<?php

namespace Paraunit\Tests\Unit\Printer;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Printer\ProcessPrinter;
use Paraunit\Tests\Stub\UnformattedOutputStub;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Prophecy\Argument;

class ProcessPrinterTest extends \PHPUnit_Framework_TestCase
{
    public function testPrintProcessGoesToFormatting()
    {
        $process = new StubbedParaProcess();
        $process->addTestResult('.');

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
        $process = new StubbedParaProcess();
        $process->setTestResults(array_fill(0, $times, 'F'));

        $printer = new ProcessPrinter($this->prophesize('Paraunit\Printer\SingleResultFormatter')->reveal());
        $output = $this->prophesize('Paraunit\Tests\Stub\UnformattedOutputStub');
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
