<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Tests\BaseUnitTestCase;

/**
 * Class PHPUnitConfigTest
 * @package Tests\Unit\Configuration
 */
class PHPUnitConfigTest extends BaseUnitTestCase
{
    public function testGetBaseDirectoryIsNotLazy()
    {
        $configurationFile = tempnam(null, 'phpunit_config_');
        $logsDir = '/some/tmp/dir/for/logs';

        $config = new PHPUnitConfig($this->mockFilenameFactory($configurationFile, $logsDir), null);

        $directoryPath = $config->getBaseDirectory();
        $this->assertNotEquals('', $directoryPath);
        $this->assertNotEquals('/', $directoryPath);
    }

    public function testGetFileFullPathWithDirAndUseDefaultFileName()
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs';
        $configurationFile = tempnam(null, 'phpunit_config_');
        $logsDir = '/some/tmp/dir/for/logs';

        $config = new PHPUnitConfig($this->mockFilenameFactory($configurationFile, $logsDir), $dir);

        $this->assertEquals($configurationFile, $config->getFileFullPath());

        $this->assertFileExists($configurationFile);
        $copyConfigContent = file_get_contents($configurationFile);
        $this->assertContains('<phpunit', $copyConfigContent);

        $document = new \DOMDocument();
        $document->loadXML($copyConfigContent);

        $expectedBootstrap = $config->getBaseDirectory() . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
        $this->assertEquals($expectedBootstrap, $document->documentElement->getAttribute('bootstrap'));

        $this->assertLogListenerIsPresent($document, $logsDir);
    }

    /**
     * @dataProvider configFilenameProvider
     */
    public function testGetFileFullPathWithoutDefaultFileName($file)
    {
        $configurationFile = tempnam(null, 'phpunit_config_');
        $logsDir = '/some/tmp/dir/for/logs';

        $config = new PHPUnitConfig($this->mockFilenameFactory($configurationFile, $logsDir), $file);

        $this->assertEquals($configurationFile, $config->getFileFullPath());

        $this->assertFileExists($configurationFile);
        $copyConfigContent = file_get_contents($configurationFile);
        $this->assertContains('test_only_requested_testsuite', $copyConfigContent);

        $document = new \DOMDocument();
        $document->loadXML($copyConfigContent);
        $this->assertLogListenerIsPresent($document, $logsDir);
    }

    public function configFilenameProvider()
    {
        return array(
            array($this->getStubPath() . 'StubbedXMLConfigs/stubbed_for_filter_test.xml'),
            array($this->getStubPath() . 'StubbedXMLConfigs/stubbed_with_listener.xml'),
        );
    }

    public function testGetFileFullPathWithFileDoesNotExistWillThrowException()
    {
        $dir = $this->getStubPath() . 'PHPUnitJSONLogOutput';
        $this->setExpectedException('InvalidArgumentException', PHPUnitConfig::DEFAULT_FILE_NAME . ' does not exist');

        new PHPUnitConfig($this->prophesize('Paraunit\Configuration\TempFilenameFactory')->reveal(), $dir);
    }

    public function testGetFileFullPathWithPathDoesNotExistWillThrowException()
    {
        $dir = $this->getStubPath() . 'foobar';
        $this->setExpectedException('InvalidArgumentException', 'Config path/file provided is not valid');

        new PHPUnitConfig($this->prophesize('Paraunit\Configuration\TempFilenameFactory')->reveal(), $dir);
    }

    /**
     * @param \DOMDocument $document
     * @param string $logsDir
     */
    private function assertLogListenerIsPresent(\DOMDocument $document, $logsDir)
    {
        $listenersNode = $document->documentElement->getElementsByTagName('listeners');
        $this->assertEquals(1, $listenersNode->length, 'Listeners node missing');
        $this->assertGreaterThanOrEqual(1, $listenersNode->item(0)->childNodes->length, 'No listeners registered');
        $paraunitListenerNode = $listenersNode->item(0)->lastChild;
        $this->assertEquals('Paraunit\Parser\JSON\LogPrinter', $paraunitListenerNode->getAttribute('class'));
        $this->assertEquals(1, $paraunitListenerNode->getElementsByTagName('arguments')->length);
        $arguments = $paraunitListenerNode->getElementsByTagName('arguments')->item(0);
        $this->assertGreaterThanOrEqual(1, $arguments->childNodes->length, 'Arguments missing');
        $argument = $arguments->firstChild;
        $this->assertEquals('string', $argument->nodeName);
        $this->assertEquals($logsDir, $argument->textContent);
    }

    /**
     * @param $configurationFile
     * @param $logsDir
     * @return TempFilenameFactory
     */
    private function mockFilenameFactory($configurationFile, $logsDir)
    {
        $filenameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $filenameFactory->getFilenameForConfiguration()
            ->willReturn($configurationFile);
        $filenameFactory->getPathForLog()
            ->willReturn($logsDir);

        return $filenameFactory->reveal();
    }
}
