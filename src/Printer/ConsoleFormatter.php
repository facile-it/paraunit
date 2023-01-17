<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\BeforeEngineStart;
use Paraunit\Printer\ValueObject\OutputStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleFormatter extends AbstractPrinter implements EventSubscriberInterface
{
    /**
     * @return array<class-string<AbstractEvent>, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEngineStart::class => 'onEngineBeforeStart',
        ];
    }

    public function onEngineBeforeStart(): void
    {
        $this->setStyle(OutputStyle::Ok, 'green');
        $this->setStyle(OutputStyle::Skip, 'yellow');
        $this->setStyle(OutputStyle::Warning, 'yellow');
        $this->setStyle(OutputStyle::Incomplete, 'blue');
        $this->setStyle(OutputStyle::Error, 'red');
        $this->setStyle(OutputStyle::Abnormal, 'magenta');
    }

    private function setStyle(OutputStyle $style, string $color): void
    {
        $formatter = $this->getOutput()->getFormatter();
        $formatter->setStyle($style->value, $this->createNewStyle($color));
    }

    private function createNewStyle(string $color): OutputFormatterStyle
    {
        return new OutputFormatterStyle($color, null, ['bold']);
    }
}
