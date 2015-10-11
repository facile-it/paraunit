<?php

namespace Paraunit\Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitConfigFile;

class PHPUnitConfigFileTest extends \PHPUnit_Framework_TestCase
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

        $config = new PHPUnitConfigFile($dir);

        $filePath = $config->getFileFullPath();
        $this->assertStringEndsWith(PHPUnitConfigFile::DEFAULT_FILE_NAME, $filePath);

        $directoryPath = $config->getDirectory();
        $this->assertStringEndsWith($dir, $directoryPath);
    }

    public function testRelativeFilenameDoesNotUseDefaultFileName()
    {
        $file = 'StubbedXMLConfigs/stubbed_for_filter_test.xml';

        $config = new PHPUnitConfigFile($file);

        $filePath = $config->getFileFullPath();

        $this->assertStringEndsNotWith(PHPUnitConfigFile::DEFAULT_FILE_NAME, $filePath);
    }

    public function testRelativeDirectoryAndDefaultFileDoesNotExistThrowException()
    {
        $dir = 'PHPUnitOutput';

        $this->setExpectedException('InvalidArgumentException', PHPUnitConfigFile::DEFAULT_FILE_NAME . ' does not exist');

        $config = new PHPUnitConfigFile($dir);
    }

    public function testInvalidDirectoryProvidedThrowException()
    {
        $dir = 'foobar';

        $this->setExpectedException('InvalidArgumentException', 'Config path/file provided is not valid');

        $config = new PHPUnitConfigFile($dir);
    }
}
