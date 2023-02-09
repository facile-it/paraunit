<?php

declare(strict_types=1);

namespace Tests;

abstract class BaseUnitTestCase extends BaseTestCase
{
    protected function getWrongCoverageStubFilePath(): string
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/WrongCoverageStub.php';
        $this->assertFileExists($filename, 'WrongCoverageStub file missing!');

        return $filename;
    }

    protected function removeDirectory(string $path): bool
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $realPath = $file->getRealPath();
            if (! $realPath) {
                continue;
            }
            if ($file->isDir()) {
                $this->removeDirectory($realPath);
            } else {
                unlink($realPath);
            }
        }

        return rmdir($path);
    }
}
