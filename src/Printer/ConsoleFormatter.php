<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\BeforeEngineStart;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleFormatter extends AbstractPrinter implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEngineStart::class => 'onEngineBeforeStart',
        ];
    }

    public function onEngineBeforeStart(): void
    {
        $formatter = $this->getOutput()->getFormatter();
        $formatter->setStyle('ok', $this->createNewStyle('green'));
        $formatter->setStyle('skip', $this->createNewStyle('yellow'));
        $formatter->setStyle('warning', $this->createNewStyle('yellow'));
        $formatter->setStyle('incomplete', $this->createNewStyle('blue'));
        $formatter->setStyle('fail', $this->createNewStyle('red'));
        $formatter->setStyle('error', $this->createNewStyle('red'));
        $formatter->setStyle('abnormal', $this->createNewStyle('magenta'));
    }

    private function createNewStyle(string $color): OutputFormatterStyle
    {
        return new OutputFormatterStyle($color, null, ['bold']);
    }
}
