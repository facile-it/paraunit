<?php
declare(strict_types=1);

namespace Tests;

use Paraunit\Configuration\EnvVariables;
use Paraunit\File\Cleaner;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseTestCase
 * @package Tests
 */
class BaseTestCase extends TestCase
{
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

    protected function createRandomTmpDir()
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

    protected function tearDown()
    {
        putenv(EnvVariables::LOG_DIR);
        putenv(EnvVariables::PROCESS_UNIQUE_ID);

        if ($this->randomTempDir && is_dir($this->randomTempDir)) {
            Cleaner::cleanUpDir($this->randomTempDir);
        }

        parent::tearDown();
    }
}
