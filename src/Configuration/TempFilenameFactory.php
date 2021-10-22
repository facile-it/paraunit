<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\File\TempDirectory;

class TempFilenameFactory
{
    /** @var TempDirectory */
    private $tempDirectory;

    public function __construct(TempDirectory $tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getPathForLog(): string
    {
        return $this->getTempSubDir('logs');
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getFilenameForLog(string $uniqueId): string
    {
        return $this->getTempFilename('logs', $uniqueId, 'json.log');
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getFilenameForCoverage(string $uniqueId): string
    {
        return $this->getTempFilename('coverage', $uniqueId, 'php');
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getFilenameForConfiguration(): string
    {
        return $this->getTempFilename('config', 'phpunit', 'xml');
    }

    /**
     * @phpstan-return non-empty-string
     */
    private function getTempFilename(string $subDir, string $filename, string $extension): string
    {
        return $this->getTempSubDir($subDir)
            . $filename
            . '.'
            . $extension;
    }

    /**
     * @phpstan-return non-empty-string
     */
    private function getTempSubDir(string $subDir): string
    {
        return $this->tempDirectory->getTempDirForThisExecution()
            . DIRECTORY_SEPARATOR
            . $subDir
            . DIRECTORY_SEPARATOR;
    }
}
