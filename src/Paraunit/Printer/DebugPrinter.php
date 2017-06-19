<?php
declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Process\AbstractParaunitProcess;

/**
 * Class DebugPrinter
 * @package Paraunit\Printer
 */
class DebugPrinter
{
    /**
     * @param AbstractParaunitProcess[] $runningStack
     * @param AbstractParaunitProcess $launchedProcess
     */
    public static function printDebugOutput(AbstractParaunitProcess $launchedProcess, array $runningStack)
    {
        echo "\n New running stack:";
        echo "\n STARTED :" . $launchedProcess->getCommandLine();
        echo "\n --------";
        foreach ($runningStack as $processRunning) {
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
