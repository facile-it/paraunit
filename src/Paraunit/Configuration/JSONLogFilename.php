<?php

namespace Paraunit\Configuration;

use Paraunit\File\TempDirectory;
use Paraunit\Process\ParaunitProcessInterface;

/**
 * Class JSONLogFilename
 * @package Paraunit\Tests\Unit\Parser
 */
class JSONLogFilename
{
    /** @var  TempDirectory */
    private $tempDirectory;

    /**
     * JSONLogFilename constructor.
     * @param TempDirectory $tempDirectory
     */
    public function __construct(TempDirectory $tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }

    public function generate(ParaunitProcessInterface $process)
    {
        return $this->generateFromUniqueId($process->getUniqueId());
    }

    public function generateFromUniqueId($uniqueId)
    {
        return $this->tempDirectory->getTempDirForThisExecution() . '/logs/' . $uniqueId . '.json.log';
    }
}
