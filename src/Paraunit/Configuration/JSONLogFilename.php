<?php

namespace Paraunit\Configuration;
use Paraunit\Process\ParaunitProcessInterface;


/**
 * Class JSONLogFilename
 * @package Paraunit\Tests\Unit\Parser
 */
class JSONLogFilename
{
    /** @var  Paraunit */
    private $configuration;

    /**
     * JSONLogFilename constructor.
     * @param Paraunit $configuration
     */
    public function __construct(Paraunit $configuration)
    {
        $this->configuration = $configuration;
    }

    public function generate(ParaunitProcessInterface $process)
    {
        return $this->generateFromUniqueId($process->getUniqueId());
    }

    public function generateFromUniqueId($uniqueId)
    {
        return $this->configuration->getTempDirForThisExecution() . '/logs/' . $uniqueId . '.json.log';
    }
}
