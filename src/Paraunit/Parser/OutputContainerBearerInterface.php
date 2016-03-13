<?php

namespace Paraunit\Parser;

use Paraunit\Printer\OutputContainerInterface;

/**
 * Interface OutputContainerBearerInterface
 * @package Paraunit\Parser
 */
interface OutputContainerBearerInterface
{
    /**
     * @return OutputContainerInterface
     */
    public function getOutputContainer();
}
