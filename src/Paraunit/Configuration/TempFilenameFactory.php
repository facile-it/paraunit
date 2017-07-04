<?php
declare(strict_types=1);

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
        return $this->getTempSubDir('logs');
    }

    /**
     * @param string $uniqueId
     * @return string
     */
    public function getFilenameForLog(string $uniqueId): string
    {
        return $this->getTempFilename('logs', $uniqueId, 'json.log');
    }

    public function getFilenameForCoverage(string $uniqueId): string
    {
        return $this->getTempFilename('coverage', $uniqueId, 'php');
    }

    public function getFilenameForConfiguration(): string
    {
        return $this->getTempFilename('config', 'phpunit', 'xml.dist');
    }

    private function getTempFilename(string $subDir, string $filename, string $extension): string
    {
        return $this->getTempSubDir($subDir)
            . $filename
            . '.'
            . $extension;
    }

    private function getTempSubDir(string $subDir): string
    {
        return $this->tempDirectory->getTempDirForThisExecution()
            . DIRECTORY_SEPARATOR
            . $subDir
            . DIRECTORY_SEPARATOR;
    }
}
