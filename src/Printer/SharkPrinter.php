<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Bin\Paraunit;
use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\BeforeEngineStart;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SharkPrinter extends AbstractPrinter implements EventSubscriberInterface
{
    /** @var bool */
    private $showLogo;

    public function __construct(OutputInterface $output, bool $showLogo)
    {
        parent::__construct($output);

        $this->showLogo = $showLogo;
    }

    /**
     * @return array<class-string<AbstractEvent>, array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEngineStart::class => ['onEngineBeforeStart', 1000],
        ];
    }

    public function onEngineBeforeStart(): void
    {
        $output = $this->getOutput();

        if ($this->showLogo) {
            $output->writeln('                                                   B>                           ');
            $output->writeln('                                                   B "Bp                        ');
            $output->writeln('.pp..                                              B    9p                      ');
            $output->writeln(' "9BBBBBBpp.                                       B      9p                    ');
            $output->writeln('    " ""9BBBBBBpp                          .<eeP"B B      .B b                  ');
            $output->writeln('           "SANDROBpp              .     B B     B B      )B B                  ');
            $output->writeln('              "BFRABBBB>  .<pe6P\B B     B B     B B      $  B     .e           ');
            $output->writeln('                 5NICOBBB B     ·B B     B B     B Bqp.  :B  B     $ 4BBpp      ');
            $output->writeln('                   BMIKIB B        B     B B     B B   "^Bp  B    ) |BBB"\BBpp. ');
            $output->writeln('                 .BALEBBB """9q.   B"""""B B"""""B B      1p B""""9p BBBBbBBBBBBB');
            $output->writeln('               <BLUCABBBB B    "B  B     B B     B B       B B     9 9BBB< ^P"  ');
            $output->writeln('            .6BSERGIOBBBB B666666B B     B B     B B       9 P      7 9BBBBP    ');
        }

        $output->writeln('');
        $output->writeln('PARAUNIT v.' . Paraunit::getVersion() . ' (PHPUnit v.' . Paraunit::getPHPUnitVersion() . ')');
        $output->writeln('by Francesco Panina, Alessandro Lai & Shark Dev Team @ Facile.it');
    }
}
