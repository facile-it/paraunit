<?php

namespace Paraunit\Configuration;

use Paraunit\File\TempDirectory;

/**
 * Class TempFilenameFactory
 * @package Tests\Unit\Parser
 */
class TempFilenameFactory
{
    /** @var TempDirectory */
    private $tempDirectory;

    /**
     * TempFilenameFactory constructor.
     * @param TempDirectory $tempDirectory
     */
    public function __construct(TempDirectory $tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }

    public function getPathForLog(): string
    {
        return $this->getTempFilename('logs', '', '');
    }

    public function getFilenameForLog(string $uniqueId): string
    {
        return $this->getTempFilename('logs', $uniqueId, '.json.log');
    }

    public function getFilenameForCoverage(string $uniqueId): string
    {
        return $this->getTempFilename('coverage', $uniqueId, '.php');
    }

    public function getFilenameForConfiguration(): string
    {
        return $this->getTempFilename('config', 'phpunit', '.xml.dist');
    }

    /**
     * @param string $subdir
     * @param string $filename
     * @param string $extension
     * @return string
     * @throws \RuntimeException
     */
    private function getTempFilename(string $subdir, string $filename, string $extension): string
    {
        return $this->tempDirectory->getTempDirForThisExecution()
            . DIRECTORY_SEPARATOR
            . $subdir
            . DIRECTORY_SEPARATOR
            . $filename
            . $extension;
    }
}
