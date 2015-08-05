<?php

namespace Paraunit\Lifecycle;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

/***
 * Class EngineEvent
 * @package Paraunit\Lifecycle
 */
class EngineEvent extends Event
{
    // This Event will be triggered before the whole paraunit engine is started
    const BEFORE_START = 'engine_event.before_start';

    /** @var  array */
    protected $files;

    /** @var  OutputInterface */
    protected $outputInterface;

    /**
     * EngineEvent constructor.
     * @param $files
     * @param OutputInterface $outputInterface
     */
    public function __construct($files, OutputInterface $outputInterface)
    {
        $this->files = $files;
        $this->outputInterface = $outputInterface;
    }

    public static function buildFromContext($files, OutputInterface $outputInteface){

        return new EngineEvent($files, $outputInteface);

    }

    /**
     * @return OutputInterface
     */
    public function getOutputInterface(){
        return $this->outputInterface;
    }

}
