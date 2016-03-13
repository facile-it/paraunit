<?php

namespace Paraunit\Lifecycle;

use Symfony\Component\Console\Output\OutputInterface;

/***
 * Class EngineEvent
 * @package Paraunit\Lifecycle
 */
class EngineEvent extends AbstractEvent
{
    // This Event will be triggered before the whole paraunit engine is started
    const BEFORE_START = 'engine_event.before_start';

    // This Event will be triggered when paraunit finished building the process stack
    const START = 'engine_event.start';

    // This Event will be triggered when paraunit finished all test execution
    const END = 'engine_event.end';

    /** @var  OutputInterface */
    protected $outputInterface;

    /**
     * @param OutputInterface $outputInterface
     * @param array $context
     */
    public function __construct(OutputInterface $outputInterface, $context = array())
    {
        $this->outputInterface = $outputInterface;
        $this->context = $context;
    }

    /**
     * @return OutputInterface
     */
    public function getOutputInterface()
    {
        return $this->outputInterface;
    }
}
