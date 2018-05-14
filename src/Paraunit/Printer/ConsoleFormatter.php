<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ConsoleFormatter
 * @package Paraunit\Printer
 */
class ConsoleFormatter extends AbstractPrinter implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            EngineEvent::BEFORE_START => 'onEngineBeforeStart',
        ];
    }

    public function onEngineBeforeStart()
    {
        $formatter = $this->getOutput()->getFormatter();

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
        return new OutputFormatterStyle($color, null, ['bold']);
    }
}
