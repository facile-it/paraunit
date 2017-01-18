<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitConfig;
use Tests\BaseUnitTestCase;

/**
 * Class PHPUnitConfigTest
 * @package Tests\Unit\Configuration
 */
class PHPUnitConfigTest extends BaseUnitTestCase
{
    public function testRelativeDirectoryDoesUseDefaultFileName()
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs';
        $configurationFile = tempnam(null, 'phpunit_config_');
        $logsDir = '/some/tmp/dir/for/logs';
        $filenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $filenameFactory->getFilenameForConfiguration()
            ->willReturn($configurationFile);
        $filenameFactory->getPathForLog()
            ->willReturn($logsDir);

        $config = new PHPUnitConfig($filenameFactory->reveal(), $dir);

        $this->assertEquals($configurationFile, $config->getFileFullPath());

        $directoryPath = $config->getBaseDirectory();
        $this->assertNotEquals(dirname($configurationFile), $directoryPath);
        $this->assertEquals(dirname($dir), $directoryPath);

        $this->assertFileExists($configurationFile);
        $copyConfigContent = file_get_contents($configurationFile);
        $this->assertContains('<phpunit', $copyConfigContent);

        $document = new \DOMDocument();
        $document->loadXML($copyConfigContent);

        $this->assertEquals($logsDir, $document->documentElement->getAttribute('printerFile'));
        $this->assertEquals('Paraunit\Parser\JSON\LogPrinter', $document->documentElement->getAttribute('printerClass'));
    }

    public function testRelativeFilenameDoesNotUseDefaultFileName()
    {
        $file = $this->getStubPath() . 'StubbedXMLConfigs/stubbed_for_filter_test.xml';
        $configurationFile = tempnam(null, 'phpunit_config_');
        $logsDir = '/some/tmp/dir/for/logs';
        $filenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $filenameFactory->getFilenameForConfiguration()
            ->willReturn($configurationFile);
        $filenameFactory->getPathForLog()
            ->willReturn($logsDir);

        $config = new PHPUnitConfig($filenameFactory->reveal(), $file);

        $this->assertEquals($configurationFile, $config->getFileFullPath());

        $this->assertFileExists($configurationFile);
        $copyConfigContent = file_get_contents($configurationFile);
        $this->assertContains('test_only_requested_testsuite', $copyConfigContent);
    }

    public function testRelativeDirectoryAndDefaultFileDoesNotExistThrowException()
    {
        $dir = $this->getStubPath() . 'PHPUnitJSONLogOutput';

        $this->setExpectedException('InvalidArgumentException', PHPUnitConfig::DEFAULT_FILE_NAME . ' does not exist');

        new PHPUnitConfig($this->prophesize('Paraunit\Configuration\TempFilenameFactory')->reveal(), $dir);
    }

    public function testInvalidDirectoryProvidedThrowException()
    {
        $dir = $this->getStubPath() . 'foobar';

        $this->setExpectedException('InvalidArgumentException', 'Config path/file provided is not valid');

        new PHPUnitConfig($this->prophesize('Paraunit\Configuration\TempFilenameFactory')->reveal(), $dir);
    }

    /**
     * @return string
     */
    private function getStubPath()
    {
        return realpath(__DIR__ . '/../../Stub') . DIRECTORY_SEPARATOR;
    }
}
