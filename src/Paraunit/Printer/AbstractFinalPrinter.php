<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Parser\JSONLogParser;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractFinalPrinter
 * @package Paraunit\Printer
 */
abstract class AbstractFinalPrinter
{
    /** @var  JSONLogParser */
    protected $logParser;

    /** @var  OutputInterface */
    protected $output;

    /**
     * FinalPrinter constructor.
     * @param JSONLogParser $logParser
     */
    public function __construct(JSONLogParser $logParser)
    {
        $this->logParser = $logParser;
    }

    /**
     * @param EngineEvent $engineEvent
     */
    abstract public function onEngineEnd(EngineEvent $engineEvent);
}
