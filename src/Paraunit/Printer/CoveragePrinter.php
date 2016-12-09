<?php


namespace Paraunit\Printer;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Proxy\XDebugProxy;

/**
 * Class CoveragePrinter
 * @package Paraunit\Printer
 */
class CoveragePrinter
{
    /** @var  PHPDbgBinFile */
    private $phpdgbBin;
    
    /** @var  XDebugProxy */
    private $xdebug;

    /**
     * CoveragePrinter constructor.
     * @param PHPDbgBinFile $phpdgbBin
     * @param XDebugProxy $xdebug
     */
    public function __construct(PHPDbgBinFile $phpdgbBin, XDebugProxy $xdebug)
    {
        $this->phpdgbBin = $phpdgbBin;
        $this->xdebug = $xdebug;
    }

    public function onEngineBeforeStart(EngineEvent $engineEvent)
    {
        $output = $engineEvent->getOutputInterface();

        $output->write('Coverage driver in use: ');

        if ($this->phpdgbBin->isAvailable()) {
            $output->writeln('PHPDBG');

            if ($this->xdebug->isLoaded()) {
                $output->writeln('WARNING: both drivers found (PHPDBG, xDebug); this may lead to memory exhaustion');
            }
        }

        if ($this->xdebug->isLoaded()) {
            $output->writeln('xDebug');
        }
    }
}
