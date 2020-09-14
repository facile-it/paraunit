<?php

declare(strict_types=1);

namespace Tests;

use Paraunit\Configuration\EnvVariables;
use Paraunit\File\Cleaner;
use Paraunit\Proxy\Coverage\FakeDriver;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;

class BaseTestCase extends TestCase
{
    use ProphecyTrait;

    /** @var string|null */
    private $randomTempDir;

    protected function getCoverageStubFilePath(): string
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/Coverage4Stub.php';
        static::assertFileExists($filename, 'CoverageStub file missing!');

        return $filename;
    }

    protected function getConfigForStubs(): string
    {
        return $this->getStubPath() . 'phpunit_for_stubs.xml';
    }

    protected function getConfigForDeprecationListener(): string
    {
        return $this->getStubPath() . 'phpunit_with_deprecations.xml';
    }

    protected function getStubPath(): string
    {
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . 'Stub') . DIRECTORY_SEPARATOR;
    }

    protected function createRandomTmpDir(): void
    {
        $this->randomTempDir = uniqid(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'paraunit-test-', true);
        $this->randomTempDir .= DIRECTORY_SEPARATOR;

        $this->assertTrue(
            putenv(EnvVariables::LOG_DIR . '=' . $this->randomTempDir),
            'Failed setting env variable for log dir'
        );
    }

    protected function getRandomTempDir(): string
    {
        $this->assertNotNull($this->randomTempDir, 'Tmp dir not initialized');

        return $this->randomTempDir;
    }

    protected function tearDown(): void
    {
        putenv(EnvVariables::LOG_DIR);
        putenv(EnvVariables::PROCESS_UNIQUE_ID);

        if ($this->randomTempDir && is_dir($this->randomTempDir)) {
            try {
                Cleaner::cleanUpDir($this->randomTempDir);
            } catch (\Throwable $exception) {
                $this->addWarning('Error while cleaning up temp folders: ' . $exception->getMessage());
            }
        }

        parent::tearDown();
    }

    protected function createCodeCoverage(): CodeCoverage
    {
        return new CodeCoverage(new FakeDriver(), new Filter());
    }

    protected function getFileContent(string $filePath): string
    {
        $this->assertFileExists($filePath);
        $content = file_get_contents($filePath);
        if (! \is_string($content)) {
            $this->fail('Unable to retrieve file content from ' . $filePath);
        }

        return $content;
    }
}
