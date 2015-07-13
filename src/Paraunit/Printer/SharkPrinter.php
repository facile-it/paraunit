<?php

namespace Paraunit\Printer;

use Symfony\Component\Console\Output\OutputInterface;

class SharkPrinter
{
    public function printSharkLogo(OutputInterface $outputInterface)
    {
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
        $outputInterface->writeln('PARAUNIT TEST 0.4 -- by Shark Dev Team @ Facile.it');
    }
}