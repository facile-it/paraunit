<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\File\TempDirectory;

/**
 * Class TempFilenameFactoryTest
 * @package Tests\Unit\Configuration
 */
class TempFilenameFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPathForLog()
    {
        $tempDir = new TempDirectory();
        $tempFileNameFactory = new TempFilenameFactory($tempDir);

        $pathForLog = $tempFileNameFactory->getPathForLog();

        $expected = $tempDir->getTempDirForThisExecution()
            . DIRECTORY_SEPARATOR
            . 'logs'
            . DIRECTORY_SEPARATOR;

        $this->assertEquals($expected, $pathForLog);
    }

    public function testGetFilenameForLog()
    {
        $processUniqueId = 'asdasdasdasd';
        $tempDir = new TempDirectory();
        $tempFileNameFactory = new TempFilenameFactory($tempDir);

        $filenameForLog = $tempFileNameFactory->getFilenameForLog($processUniqueId);

        $expected = $tempDir->getTempDirForThisExecution()
            . DIRECTORY_SEPARATOR
            . 'logs'
            . DIRECTORY_SEPARATOR
            . $processUniqueId
            . '.json.log';

        $this->assertEquals($expected, $filenameForLog);
        $this->assertStringStartsWith($tempFileNameFactory->getPathForLog(), $filenameForLog);
    }

    public function testGetFilenameForCoverage()
    {
        $processUniqueId = 'asdasdasdasd';
        $tempDir = new TempDirectory();
        $tempFileNameFactory = new TempFilenameFactory($tempDir);

        $filenameForCoverage = $tempFileNameFactory->getFilenameForCoverage($processUniqueId);

        $expected = $tempDir->getTempDirForThisExecution()
            . DIRECTORY_SEPARATOR
            . 'coverage'
            . DIRECTORY_SEPARATOR
            . $processUniqueId
            . '.php';

        $this->assertEquals($expected, $filenameForCoverage);
    }

    public function testGetFilenameForConfiguration()
    {
        $tempDir = new TempDirectory();
        $tempFileNameFactory = new TempFilenameFactory($tempDir);

        $filenameForConfiguration = $tempFileNameFactory->getFilenameForConfiguration();

        $expected = $tempDir->getTempDirForThisExecution()
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . PHPUnitConfig::DEFAULT_FILE_NAME;

        $this->assertEquals($expected, $filenameForConfiguration);
    }
}
