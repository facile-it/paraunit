<?php

namespace Paraunit\File;

/**
 * Class TempDirectory
 * @package Paraunit\File
 */
class TempDirectory
{
    private static $tempDirs = array(
        '/dev/shm',
        '/temp',
    );

    /** @var  string */
    private $timestamp;

    /**
     * Paraunit constructor.
     */
    public function __construct()
    {
        $this->timestamp = uniqid(date('Ymd-His'));
    }

    /**
     * @return string
     */
    public function getTempDirForThisExecution()
    {
        $dir = self::getTempBaseDir() . DIRECTORY_SEPARATOR . $this->timestamp;
        self::mkdirIfNotExists($dir);
        self::mkdirIfNotExists($dir . DIRECTORY_SEPARATOR . 'logs');
        self::mkdirIfNotExists($dir . DIRECTORY_SEPARATOR . 'coverage');

        return $dir;
    }

    /**
     * @return string
     */
    public static function getTempBaseDir()
    {
        foreach (self::$tempDirs as $directory) {
            if (file_exists($directory)) {
                $baseDir = $directory . DIRECTORY_SEPARATOR . 'paraunit';
                self::mkdirIfNotExists($baseDir);

                return $baseDir;
            }
        }

        throw new \RuntimeException('Unable to create a temporary directory');
    }

    /**
     * @param string $path
     */
    private static function mkdirIfNotExists($path)
    {
        if ( ! file_exists($path)) {
            mkdir($path);
        }
    }
}
