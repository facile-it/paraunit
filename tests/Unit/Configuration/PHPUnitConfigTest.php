<?php
declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\StaticOutputPath;
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
        $logsDir = '/some/tmp/dir/for/logs';

        $config = new PHPUnitConfig($this->mockFilenameFactory($logsDir), '');

        $directoryPath = $config->getBaseDirectory();
        $this->assertNotEquals('', $directoryPath);
        $this->assertNotEquals('/', $directoryPath);
    }

    public function testGetFileFullPathWithDirAndUseDefaultFileName()
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs';
        $configurationFile = $dir . DIRECTORY_SEPARATOR . 'phpunit-paraunit.xml';
        $logsDir = '/some/tmp/dir/for/logs';

        $config = new PHPUnitConfig($this->mockFilenameFactory($logsDir), $dir);

        $this->assertEquals($configurationFile, $config->getFileFullPath());

        $this->assertFileExists($configurationFile);
        $copyConfigContent = file_get_contents($configurationFile);
        $this->assertContains('<phpunit', $copyConfigContent);

        $document = new \DOMDocument();
        $document->loadXML($copyConfigContent);

        $expectedBootstrap = $config->getBaseDirectory() . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
        $this->assertEquals($expectedBootstrap, $document->documentElement->getAttribute('bootstrap'));

        $this->assertListenerIsPresent($document, $logsDir);
    }

    /**
     * @dataProvider configFilenameProvider
     */
    public function testGetFileFullPathWithoutDefaultFileName(string $file)
    {
        $expectedConfigurationFile = dirname($file) . DIRECTORY_SEPARATOR . 'phpunit-paraunit.xml';
        $logsDir = '/some/tmp/dir/for/logs';

        $config = new PHPUnitConfig($this->mockFilenameFactory($logsDir), $file);

        $this->assertEquals($expectedConfigurationFile, $config->getFileFullPath());

        $this->assertFileExists($expectedConfigurationFile);
        $copyConfigContent = file_get_contents($expectedConfigurationFile);
        $this->assertContains('test_only_requested_testsuite', $copyConfigContent);

        $document = new \DOMDocument();
        $document->loadXML($copyConfigContent);
        $this->assertListenerIsPresent($document, $logsDir);
    }

    /**
     * @return string[]
     */
    public function configFilenameProvider(): array
    {
        return [
            [$this->getStubPath() . 'StubbedXMLConfigs/stubbed_for_filter_test.xml'],
            [$this->getStubPath() . 'StubbedXMLConfigs/stubbed_with_listener.xml'],
        ];
    }

    public function testGetFileFullPathWithFileDoesNotExistWillThrowException()
    {
        $dir = $this->getStubPath() . 'PHPUnitJSONLogOutput';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(PHPUnitConfig::DEFAULT_FILE_NAME . ' does not exist');

        new PHPUnitConfig($this->prophesize(TempFilenameFactory::class)->reveal(), $dir);
    }

    public function testGetFileFullPathWithPathDoesNotExistWillThrowException()
    {
        $dir = $this->getStubPath() . 'foobar';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Config path/file provided is not valid');

        new PHPUnitConfig($this->prophesize(TempFilenameFactory::class)->reveal(), $dir);
    }

    /**
     * @param \DOMDocument $document
     * @param string $logsDir
     */
    private function assertListenerIsPresent(\DOMDocument $document, $logsDir)
    {
        $listenersNode = $document->documentElement->getElementsByTagName('listeners');
        $this->assertEquals(1, $listenersNode->length, 'Listeners node missing');
        $this->assertGreaterThanOrEqual(1, $listenersNode->item(0)->childNodes->length, 'No listeners registered');

        $paraunitListenerNode = $listenersNode->item(0)->lastChild;
        $this->assertEquals(StaticOutputPath::class, $paraunitListenerNode->getAttribute('class'));
        $this->assertEquals(1, $paraunitListenerNode->getElementsByTagName('arguments')->length);

        $arguments = $paraunitListenerNode->getElementsByTagName('arguments')->item(0);
        $this->assertGreaterThanOrEqual(1, $arguments->childNodes->length, 'Arguments missing');

        $argument = $arguments->firstChild;
        $this->assertEquals('string', $argument->nodeName);
        $this->assertEquals($logsDir, $argument->textContent);
    }

    private function mockFilenameFactory(string $logsDir): TempFilenameFactory
    {
        $filenameFactory = $this->prophesize(TempFilenameFactory::class);
        $filenameFactory->getPathForLog()
            ->willReturn($logsDir);

        return $filenameFactory->reveal();
    }

    protected function tearDown()
    {
        $copiedConfig = implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            '..',
            'Stub',
            'StubbedXMLConfigs',
            PHPUnitConfig::COPY_FILE_NAME
        ]);

        if (file_exists($copiedConfig)) {
            unlink($copiedConfig);
        }

        parent::tearDown();
    }
}
