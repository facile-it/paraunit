<?php

namespace Paraunit\Parser;

use Paraunit\Configuration\JSONLogFilename;
use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Process\ParaunitProcessInterface;

/**
 * Class JSONLogFetcher
 * @package Paraunit\Parser
 */
class JSONLogFetcher
{
    /** @var  JSONLogFilename */
    private $fileName;

    /**
     * JSONLogFetcher constructor.
     * @param JSONLogFilename $fileName
     */
    public function __construct(JSONLogFilename $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param ParaunitProcessInterface $process
     * @return string The full JSON log
     * @throws JSONLogNotFoundException
     */
    public function fetch(ParaunitProcessInterface $process)
    {
        $filePath = $this->fileName->generate($process);

        if ( ! file_exists($filePath)) {
            throw new JSONLogNotFoundException($process);
        }

        return file_get_contents($filePath);
    }
}
