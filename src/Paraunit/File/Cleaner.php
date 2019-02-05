<?php

declare(strict_types=1);

namespace Paraunit\File;

use Paraunit\Lifecycle\EngineEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Cleaner implements EventSubscriberInterface
{
    /** @var TempDirectory */
    private $tempDirectory;

    /**
     * @param TempDirectory $tempDirectory
     */
    public function __construct(TempDirectory $tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EngineEvent::BEFORE_START => 'purgeCurrentTempDir',
            EngineEvent::END => 'purgeCurrentTempDir',
        ];
    }

    public function purgeCurrentTempDir()
    {
        self::cleanUpDir($this->tempDirectory->getTempDirForThisExecution());
    }

    /**
     * @param string $dir
     *
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
