<?php
declare(strict_types=1);

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
    public function onEngineBeforeStart(EngineEvent $engineEvent)
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

    private function createNewStyle(string $color): OutputFormatterStyle
    {
        return new OutputFormatterStyle($color, null, ['bold', 'blink']);
    }
}
