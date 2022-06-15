<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\BeforeEngineStart;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CoveragePrinter implements EventSubscriberInterface
{
    /** @var PHPDbgBinFile */
    private $phpdgbBin;

    /** @var XDebugProxy */
    private $xdebug;

    /** @var OutputInterface */
    private $output;

    /** @var PcovProxy */
    private $pcov;

    public function __construct(PHPDbgBinFile $phpdgbBin, XDebugProxy $xdebug, PcovProxy $pcov, OutputInterface $output)
    {
        $this->phpdgbBin = $phpdgbBin;
        $this->xdebug = $xdebug;
        $this->output = $output;
        $this->pcov = $pcov;
    }

    /**
     * @return array<class-string<AbstractEvent>, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEngineStart::class => ['onEngineBeforeStart', 100],
        ];
    }

    public function onEngineBeforeStart(): void
    {
        $this->output->write('Coverage driver in use: ');

        if ($this->pcov->isLoaded()) {
            $this->output->writeln('Pcov');
        } elseif ($this->xdebug->isLoaded()) {
            $this->output->writeln('Xdebug');
        } elseif ($this->phpdgbBin->isAvailable()) {
            $this->output->writeln('PHPDBG');
        } else {
            $this->output->writeln('NO COVERAGE DRIVER FOUND!');
        }
    }
}
