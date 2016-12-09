<?php


namespace Paraunit\Printer;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Lifecycle\EngineEvent;

/**
 * Class CoveragePrinter
 * @package Paraunit\Printer
 */
class CoveragePrinter
{
    /** @var  PHPDbgBinFile */
    private $phpdgbBin;

    /**
     * CoveragePrinter constructor.
     * @param PHPDbgBinFile $phpdgbBin
     */
    public function __construct(PHPDbgBinFile $phpdgbBin)
    {
        $this->phpdgbBin = $phpdgbBin;
    }

    public function onEngineBeforeStart(EngineEvent $engineEvent)
    {
        $output = $engineEvent->getOutputInterface();

        $output->write('Coverage driver in use: ');

        if ($this->phpdgbBin->isAvailable()) {
            $output->writeln('PHPDBG');

            if (extension_loaded('xdebug')) {
                $output->writeln('WARNING: both drivers found (PHPDBG, xDebug); this may lead to memory exhaustion');
            }
        }

        if (extension_loaded('xdebug')) {
            $output->writeln('xDebug');
        }
    }
}
