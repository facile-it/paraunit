<?php

namespace Paraunit\Configuration;
use Paraunit\Process\ParaunitProcessInterface;


/**
 * Class JSONLogFilename
 * @package Paraunit\Tests\Unit\Parser
 */
class JSONLogFilename
{
    public function generate(ParaunitProcessInterface $process)
    {
        return $this->generateFromUniqueId($process->getUniqueId());
    }

    public function generateFromUniqueId($uniqueId)
    {
        return '/dev/shm/paraunit/logs/' . $uniqueId . '.json.log';
    }
}
