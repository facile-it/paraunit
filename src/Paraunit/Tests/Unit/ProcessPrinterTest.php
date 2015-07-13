<?php

namespace Paraunit\Tests\Unit;


use Paraunit\Printer\ProcessPrinter;
use Paraunit\Tests\Stub\ConsoleOutputStub;
use Paraunit\Tests\Stub\StubbedParaProcess;

class ProcessPrinterTest extends \PHPUnit_Framework_TestCase
{
    public function testPrintProcessResultWithRetry()
    {
        $process = new StubbedParaProcess();
        $process->setIsToBeRetried(true);

        $printer = new ProcessPrinter();
        $output = new ConsoleOutputStub();

        $printer->printProcessResult($output, $process);

        $this->assertEquals('<ok>A</ok>', $output->getOutput());
    }

    public function testPrintProcessResultWithSegFault()
    {
        $process = new StubbedParaProcess();
        $process->addSegmentationFault('test');

        $printer = new ProcessPrinter();
        $output = new ConsoleOutputStub();

        $printer->printProcessResult($output, $process);

        $this->assertEquals('<error>X</error>', $output->getOutput());
    }

    public function testPrintProcessResultWithFatalError()
    {
        $process = new StubbedParaProcess();
        $process->addFatalError('test');

        $printer = new ProcessPrinter();
        $output = new ConsoleOutputStub();

        $printer->printProcessResult($output, $process);

        $this->assertEquals('<error>X</error>', $output->getOutput());
    }

    public function testPrintProcessResultUnknownResult()
    {
        $process = new StubbedParaProcess();

        $printer = new ProcessPrinter();
        $output = new ConsoleOutputStub();

        $printer->printProcessResult($output, $process);

        $this->assertEquals('<error>X</error>', $output->getOutput());
    }
}
