<?php

namespace Tests\Unit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Printer\CoveragePrinter;
use phpmock\prophecy\PHPProphet;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class CoveragePrinterTest
 * @package Tests\Unit\Printer
 */
class CoveragePrinterTest extends \PHPUnit_Framework_TestCase
{
    private $prophet;

    public function __construct()
    {
        parent::__construct();
        $this->prophet = new PHPProphet();
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

    public function testOnEngineBeforeStartWithPHPDBGEngine()
    {
        $output = new UnformattedOutputStub();
        $engineEvent = new EngineEvent($output);

        $this->mockXdebugLoaded(false);
        $phpdbgBin = $this->prophesize('Paraunit\Configuration\PHPDbgBinFile');
        $phpdbgBin->isAvailable()
            ->willReturn(true);
        $printer = new CoveragePrinter($phpdbgBin->reveal());

        $printer->onEngineBeforeStart($engineEvent);

        $this->assertContains('Coverage driver in use: PHPDBG', $output->getOutput());
    }

    public function testOnEngineBeforeStartWithxDebugEngine()
    {
        $output = new UnformattedOutputStub();
        $engineEvent = new EngineEvent($output);

        $this->mockXdebugLoaded(true);
        $phpdbgBin = $this->prophesize('Paraunit\Configuration\PHPDbgBinFile');
        $phpdbgBin->isAvailable()
            ->willReturn(false);
        $printer = new CoveragePrinter($phpdbgBin->reveal());

        $printer->onEngineBeforeStart($engineEvent);

        $this->assertContains('Coverage driver in use: xDebug', $output->getOutput());
    }

    public function testOnEngineBeforeStartWithWarningForBothEnginesEnabled()
    {
        $output = new UnformattedOutputStub();
        $engineEvent = new EngineEvent($output);

        $this->mockXdebugLoaded(true);
        $phpdbgBin = $this->prophesize('Paraunit\Configuration\PHPDbgBinFile');
        $phpdbgBin->isAvailable()
            ->willReturn(true);
        $printer = new CoveragePrinter($phpdbgBin->reveal());

        $printer->onEngineBeforeStart($engineEvent);

        $this->assertContains('WARNING', $output->getOutput());
        $this->assertContains('both driver', $output->getOutput());
    }

    private function mockXdebugLoaded($shouldReturn)
    {
        $prophecy = $this->prophet->prophesize('Paraunit\Printer');
        $prophecy->extension_loaded('xdebug')
            ->willReturn($shouldReturn);

        $prophecy->reveal();
    }
}
