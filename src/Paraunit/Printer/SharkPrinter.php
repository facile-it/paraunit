<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;

/**
 * Class SharkPrinter
 * @package Paraunit\Printer
 */
class SharkPrinter
{

    /** @var string */
    protected $version;

    /**
     * @param string $version
     */
    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * @param EngineEvent $engineEvent
     */
    public function onEngineStart(EngineEvent $engineEvent)
    {
        $outputInterface = $engineEvent->getOutputInterface();

        $outputInterface->writeln('                                                   B>                           ');
        $outputInterface->writeln('                                                   B "Bp                        ');
        $outputInterface->writeln('.pp..                                              B    9p                      ');
        $outputInterface->writeln(' "9BBBBBBpp.                                       B      9p                    ');
        $outputInterface->writeln('    " ""9BBBBBBpp                          .<eeP"B B      .B b                  ');
        $outputInterface->writeln('           "SANDROBpp              .     B B     B B      )B B                  ');
        $outputInterface->writeln('              "BFRABBBB>  .<pe6P\B B     B B     B B      $  B     .e           ');
        $outputInterface->writeln('                 5NICOBBB B     ·B B     B B     B Bqp.  :B  B     $ 4BBpp      ');
        $outputInterface->writeln('                   BMIKIB B        B     B B     B B   "^Bp  B    ) |BBB"\BBpp. ');
        $outputInterface->writeln('                 .BALEBBB """9q.   B"""""B B"""""B B      1p B""""9p BBBBbBBBBBBB');
        $outputInterface->writeln('               <BLUCABBBB B    "B  B     B B     B B       B B     9 9BBB< ^P"  ');
        $outputInterface->writeln('            .6BSERGIOBBBB B666666B B     B B     B B       9 P      7 9BBBBP    ');

        $outputInterface->writeln('');
        $outputInterface->writeln(
            'PARAUNIT v' .
            $this->version .
            ' - by Francesco Panina, Alessandro Lai & Shark Dev Team @ Facile.it'
        );
    }
}
