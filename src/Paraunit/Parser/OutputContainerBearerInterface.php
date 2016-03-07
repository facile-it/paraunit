<?php

namespace Paraunit\Parser;

use Paraunit\Printer\OutputContainer;

/**
 * Interface OutputContainerBearerInterface
 * @package Paraunit\Parser
 */
interface OutputContainerBearerInterface
{
    /**
     * @return OutputContainer
     */
    public function getOutputContainer();
}
