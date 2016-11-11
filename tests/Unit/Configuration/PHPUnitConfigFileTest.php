<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitConfig;

class PHPUnitConfigTest extends \PHPUnit_Framework_TestCase
{
    private $previousCWD;

    protected function setUp()
    {
        parent::setUp();

        $this->previousCWD = getcwd();

        $stubPath = realpath(__DIR__ . '/../../Stub');
        chdir($stubPath);
    }

    protected function tearDown()
    {
        chdir($this->previousCWD);
        $this->previousCWD = null;

        parent::tearDown();
    }

    public function testRelativeDirectoryDoesUseDefaultFileName()
    {
        $dir = 'StubbedXMLConfigs';

        $config = new PHPUnitConfig($dir);

        $filePath = $config->getFileFullPath();
        $this->assertStringEndsWith(PHPUnitConfig::DEFAULT_FILE_NAME, $filePath);

        $directoryPath = $config->getDirectory();
        $this->assertStringEndsWith($dir, $directoryPath);
    }

    public function testRelativeFilenameDoesNotUseDefaultFileName()
    {
        $file = 'StubbedXMLConfigs/stubbed_for_filter_test.xml';

        $config = new PHPUnitConfig($file);

        $filePath = $config->getFileFullPath();

        $this->assertStringEndsNotWith(PHPUnitConfig::DEFAULT_FILE_NAME, $filePath);
    }

    public function testRelativeDirectoryAndDefaultFileDoesNotExistThrowException()
    {
        $dir = 'PHPUnitJSONLogOutput';

        $this->setExpectedException('InvalidArgumentException', PHPUnitConfig::DEFAULT_FILE_NAME . ' does not exist');

        new PHPUnitConfig($dir);
    }

    public function testInvalidDirectoryProvidedThrowException()
    {
        $dir = 'foobar';

        $this->setExpectedException('InvalidArgumentException', 'Config path/file provided is not valid');

        new PHPUnitConfig($dir);
    }
}
