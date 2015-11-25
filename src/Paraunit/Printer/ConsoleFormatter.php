<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Class ConsoleFormatter
 * @package Paraunit\Printer
 */
class ConsoleFormatter
{
    /**
     * @param EngineEvent $engineEvent
     */
    public function onEngineStart(EngineEvent $engineEvent)
    {

        $outputInterface= $engineEvent->getOutputInterface();

        if ($outputInterface->getFormatter()) {
            $style = new OutputFormatterStyle('green', null, array('bold', 'blink'));
            $outputInterface->getFormatter()->setStyle('ok', $style);

            $style = new OutputFormatterStyle('yellow', null, array('bold', 'blink'));
            $outputInterface->getFormatter()->setStyle('skipped', $style);

            $style = new OutputFormatterStyle('blue', null, array('bold', 'blink'));
            $outputInterface->getFormatter()->setStyle('incomplete', $style);

            $style = new OutputFormatterStyle('red', null, array('bold', 'blink'));
            $outputInterface->getFormatter()->setStyle('fail', $style);

            $style = new OutputFormatterStyle('red', null, array('bold', 'blink'));
            $outputInterface->getFormatter()->setStyle('error', $style);
        }
    }
}
