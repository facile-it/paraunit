<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\AssertionFailedError;

abstract class BaseUnitTestCase extends BaseTestCase
{
    /**
     * @throws AssertionFailedError
     * @throws \Exception
     */
    protected function getLogFromStub(string $event = 'test', string $status = 'fail', string $testOutput = null): \stdClass
    {
        $jsonLogs = JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_ERROR);
        /** @var \stdClass[] $logs */
        $logs = json_decode((string) $jsonLogs, null, 512, JSON_THROW_ON_ERROR);
        foreach ($logs as $log) {
            if ($log->event === $event) {
                if ($testOutput) {
                    $log->status = $status;
                    $log->message = $testOutput;
                }

                return $log;
            }
        }

        $this->fail('Feasible log message not found for test');
    }

    protected function getWrongCoverageStubFilePath(): string
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/WrongCoverageStub.php';
        $this->assertFileExists($filename, 'WrongCoverageStub file missing!');

        return $filename;
    }

    protected function mockTestFormat(): TestResultFormat
    {
        $format = $this->prophesize(TestResultFormat::class);
        $format->getTag()
            ->willReturn('tag');

        return $format->reveal();
    }

    protected function mockTestResult(): TestResultInterface
    {
        return $this->prophesize(TestResultInterface::class)->reveal();
    }

    protected function mockPrintableTestResult(string $symbol = null): PrintableTestResultInterface
    {
        if ($symbol === null) {
            $format = $this->prophesize(TestResultFormat::class);
        } else {
            $format = $this->prophesize(TestResultWithSymbolFormat::class);
            $format->getTestResultSymbol()->willReturn($symbol);
        }

        $result = $this->prophesize(PrintableTestResultInterface::class);
        $result->getTestResultFormat()->willReturn($format->reveal());

        return $result->reveal();
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
