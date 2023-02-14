<?php

declare(strict_types=1);

namespace Tests;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestMethod;

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

    /**
     * @return LogData[]
     */
    protected function createLogsForOnePassedTest(): array
    {
        return [
            new LogData(LogStatus::Started, new Test('Foo'), '3'),
            new LogData(LogStatus::Prepared, new TestMethod('Foo', 'bar'), null),
            new LogData(LogStatus::Passed, new TestMethod('Foo', 'bar'), null),
            new LogData(LogStatus::Finished, new TestMethod('Foo', 'bar'), null),
        ];
    }
}
