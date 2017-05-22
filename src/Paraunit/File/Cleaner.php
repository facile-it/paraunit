<?php

namespace Paraunit\File;

use Paraunit\Configuration\PHPUnitConfig;

/**
 * Class Cleaner
 * @package Paraunit\File
 */
class Cleaner
{
    /** @var  TempDirectory */
    private $tempDirectory;

    /** @var PHPUnitConfig */
    private $phpunitConfig;

    /**
     * Cleaner constructor.
     * @param TempDirectory $tempDirectory
     * @param PHPUnitConfig $phpunitConfig
     */
    public function __construct(TempDirectory $tempDirectory, PHPUnitConfig $phpunitConfig)
    {
        $this->tempDirectory = $tempDirectory;
        $this->phpunitConfig = $phpunitConfig;
    }

    public function purgeCurrentTempDir()
    {
        self::cleanUpDir($this->tempDirectory->getTempDirForThisExecution());
    }

    public function deleteTempConfig()
    {
        $filename = $this->phpunitConfig->getFileFullPath();
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * @param string $dir
     * @return bool True if the directory existed and it has been deleted
     */
    public static function cleanUpDir(string $dir): bool
    {
        if (! file_exists($dir)) {
            return false;
        }

        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return rmdir($dir);
    }
}
