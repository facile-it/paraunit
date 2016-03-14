<?php

namespace Paraunit\Parser;

use Paraunit\Output\OutputContainerInterface;

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
