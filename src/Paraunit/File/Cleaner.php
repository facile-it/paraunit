<?php

namespace Paraunit\File;


use Paraunit\Configuration\Paraunit;

/**
 * Class Cleaner
 * @package Paraunit\File
 */
class Cleaner
{
    /** @var  Paraunit */
    private $configuration;

    /**
     * Cleaner constructor.
     * @param Paraunit $configuration
     */
    public function __construct(Paraunit $configuration)
    {
        $this->configuration = $configuration;
    }

    public function purgeCurrentTempDir()
    {
        self::cleanUpDir($this->configuration->getTempDirForThisExecution());
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
