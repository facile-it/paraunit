<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\TestResult\TestResultList;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractFinalPrinter
 * @package Paraunit\Printer
 */
abstract class AbstractFinalPrinter
{
    /** @var  TestResultList */
    protected $testResultList;

    /**
     * AbstractFinalPrinter constructor.
     * @param TestResultList $testResultList
     */
    public function __construct(TestResultList $testResultList)
    {
        $this->testResultList = $testResultList;
    }

    /**
     * @param EngineEvent $engineEvent
     */
    abstract public function onEngineEnd(EngineEvent $engineEvent);
}
