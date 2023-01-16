<?php

declare(strict_types=1);

namespace Paraunit\File;

use Paraunit\Lifecycle\BeforeEngineStart;
use Paraunit\Lifecycle\EngineEnd;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Cleaner implements EventSubscriberInterface
{
    public function __construct(private readonly TempDirectory $tempDirectory)
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEngineStart::class => 'purgeCurrentTempDir',
            EngineEnd::class => 'purgeCurrentTempDir',
        ];
    }

    public function purgeCurrentTempDir(): void
    {
        self::cleanUpDir($this->tempDirectory->getTempDirForThisExecution());
    }

    /**
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
                self::cleanUpDir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return rmdir($dir);
    }
}
