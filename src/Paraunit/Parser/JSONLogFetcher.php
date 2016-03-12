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
     * @return array
     * @throws JSONLogNotFoundException
     */
    public function fetch(ParaunitProcessInterface $process)
    {
        $filePath = $this->fileName->generate($process);

        if ( ! file_exists($filePath)) {
            throw new JSONLogNotFoundException($process);
        }

        return json_decode(
            $this->cleanLog(file_get_contents($filePath))
        );
    }

    /**
     * @param string $jsonString The dirty output
     * @return string            The normalized log, as an array of JSON objects
     */
    private static function cleanLog($jsonString)
    {
        $splitted = preg_replace('/\}\{/', '},{', $jsonString);

        return '[' . $splitted . ']';
    }
}
