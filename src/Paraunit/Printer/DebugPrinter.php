<?php

namespace Paraunit\Printer;

use Paraunit\Process\ParaunitProcessInterface;

/**
 * Class DebugPrinter
 * @package Paraunit\Printer
 */
class DebugPrinter
{
    /**
     * @param ParaunitProcessInterface[] $runningStack
     * @param ParaunitProcessInterface $launchedProcess
     */
    public static function printDebugOutput(ParaunitProcessInterface $launchedProcess, array $runningStack)
    {
        echo "\n New running stack:";
        echo "\n STARTED :" . $launchedProcess->getCommandLine();
        echo "\n --------";
        foreach ($runningStack as $processRunning){
            echo "\n";
            echo
            str_replace(
                '2>&1',
                '',
                $processRunning->getCommandLine()
            );
        }
        echo "\n FINISHED: ";
    }
}
