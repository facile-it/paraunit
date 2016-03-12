<?php

namespace Paraunit\Exception;
use Paraunit\Process\ParaunitProcessInterface;

/**
 * Class JSONLogNotFoundException
 * @package Paraunit\Exception
 */
class JSONLogNotFoundException extends \Exception
{
    public function __construct(ParaunitProcessInterface $process)
    {
        parent::__construct('JSON log not found for test; commandline: ' . $process->getCommandLine());
    }
}
