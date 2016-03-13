<?php

namespace Paraunit\File;

/**
 * Class Cleaner
 * @package Paraunit\File
 */
class Cleaner
{
    /** @var  TempDirectory */
    private $tempDirectory;

    /**
     * Cleaner constructor.
     * @param TempDirectory $tempDirectory
     */
    public function __construct(TempDirectory $tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }

    public function purgeCurrentTempDir()
    {
        self::cleanUpDir($this->tempDirectory->getTempDirForThisExecution());
    }

    /**
     * @param string $dir
     * @return bool True if the directory existed and it has been deleted
     */
    public static function cleanUpDir($dir)
    {
        if ( ! file_exists($dir)) {
            return false;
        }

        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return rmdir($dir);
    }
}
