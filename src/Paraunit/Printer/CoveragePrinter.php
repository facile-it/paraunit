<?php
declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Proxy\XDebugProxy;
use Symfony\Component\Console\Output\OutputInterface;

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

    /** @var OutputInterface */
    private $output;

    /**
     * CoveragePrinter constructor.
     * @param PHPDbgBinFile $phpdgbBin
     * @param XDebugProxy $xdebug
     */
    public function __construct(PHPDbgBinFile $phpdgbBin, XDebugProxy $xdebug, OutputInterface $output)
    {
        $this->phpdgbBin = $phpdgbBin;
        $this->xdebug = $xdebug;
        $this->output = $output;
    }

    public function onEngineBeforeStart()
    {
        $this->output->write('Coverage driver in use: ');

        if ($this->phpdgbBin->isAvailable()) {
            $this->output->writeln('PHPDBG');

            if ($this->xdebug->isLoaded()) {
                $this->output->writeln('WARNING: both drivers enabled; this may lead to memory exhaustion!');

                return;
            }
        }

        if ($this->xdebug->isLoaded()) {
            $this->output->writeln('xDebug');
        }
    }
}
