<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Bin\Paraunit;
use Paraunit\Lifecycle\BeforeEngineStart;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SharkPrinter implements EventSubscriberInterface
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly bool $showLogo
    ) {
    }

    /**
     * @return array<class-string, array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEngineStart::class => ['onEngineBeforeStart', 1000],
        ];
    }

    public function onEngineBeforeStart(): void
    {
        if ($this->showLogo) {
            $this->output->writeln('                                                   B>                           ');
            $this->output->writeln('                                                   B "Bp                        ');
            $this->output->writeln('.pp..                                              B    9p                      ');
            $this->output->writeln(' "9BBBBBBpp.                                       B      9p                    ');
            $this->output->writeln('    " ""9BBBBBBpp                          .<eeP"B B      .B b                  ');
            $this->output->writeln('           "SANDROBpp              .     B B     B B      )B B                  ');
            $this->output->writeln('              "BFRABBBB>  .<pe6P\B B     B B     B B      $  B     .e           ');
            $this->output->writeln('                 5NICOBBB B     ·B B     B B     B Bqp.  :B  B     $ 4BBpp      ');
            $this->output->writeln('                   BMIKIB B        B     B B     B B   "^Bp  B    ) |BBB"\BBpp. ');
            $this->output->writeln('                 .BALEBBB """9q.   B"""""B B"""""B B      1p B""""9p BBBBbBBBBBBB');
            $this->output->writeln('               <BLUCABBBB B    "B  B     B B     B B       B B     9 9BBB< ^P"  ');
            $this->output->writeln('            .6BSERGIOBBBB B666666B B     B B     B B       9 P      7 9BBBBP    ');
        }

        $this->output->writeln('');
        $this->output->writeln('PARAUNIT v.' . Paraunit::getVersion());
        $this->output->writeln('by Francesco Panina, Alessandro Lai & Shark Dev Team @ Facile.it');
    }
}
