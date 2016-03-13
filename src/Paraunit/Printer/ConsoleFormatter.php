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
        $formatter = $engineEvent->getOutputInterface()->getFormatter();

        if ($formatter) {
            $formatter->setStyle('ok', $this->createNewStyle('green'));
            $formatter->setStyle('skip', $this->createNewStyle('yellow'));
            $formatter->setStyle('warning', $this->createNewStyle('yellow'));
            $formatter->setStyle('incomplete', $this->createNewStyle('blue'));
            $formatter->setStyle('fail', $this->createNewStyle('red'));
            $formatter->setStyle('error', $this->createNewStyle('red'));
            $formatter->setStyle('abnormal', $this->createNewStyle('magenta'));
        }
    }

    /**
     * @param string $color
     * @return OutputFormatterStyle
     */
    private function createNewStyle($color)
    {
        return new OutputFormatterStyle($color, null, array('bold', 'blink'));
    }
}
