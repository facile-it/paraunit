<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\File\TempDirectory;
use Paraunit\Parser\JSON\TestHook as Hooks;
use Paraunit\Proxy\PHPUnitUtilXMLProxy;
use Tests\BaseUnitTestCase;

class PHPUnitConfigTest extends BaseUnitTestCase
{
    public function testGetBaseDirectoryIsNotLazy(): void
    {
        $config = new PHPUnitConfig(
            $this->prophesize(TempDirectory::class)->reveal(),
            $this->prophesize(PHPUnitUtilXMLProxy::class)->reveal(),
            ''
        );

        $directoryPath = $config->getBaseDirectory();
        $this->assertNotEquals('', $directoryPath);
        $this->assertNotEquals('/', $directoryPath);
        $this->assertNotEquals('C:\\', $directoryPath);
    }

    /**
     * @dataProvider validStubDirProvider
     */
    public function testGetFileFullPathWithDirAndUseDefaultFileName(string $dir): void
    {
        $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('paraunit_test_config_', true);
        mkdir($tmpDir);
        $this->assertDirectoryExists($tmpDir, 'Preassertion failed, unable to create temp dir');

        $tempDirectory = $this->prophesize(TempDirectory::class);
        $tempDirectory->getTempDirForThisExecution()
            ->willReturn($tmpDir);

        $config = new PHPUnitConfig(
            $tempDirectory->reveal(),
            new PHPUnitUtilXMLProxy(),
            $dir
        );

        $expectedFilename = $tmpDir . DIRECTORY_SEPARATOR . 'phpunit.xml';
        $this->assertEquals($expectedFilename, $config->getConfigPath());
        $this->assertFileExists($expectedFilename);
        $fileContent = file_get_contents($expectedFilename);
        $this->assertNotEmpty($config, 'Unable to read altered config');

        $xml = new \DOMDocument();
        $this->assertNotFalse($xml->loadXML($fileContent), 'Error while loading config');

        $expectedRegisteredHooks = [
            Hooks\Error::class,
            Hooks\Failure::class,
            Hooks\Incomplete::class,
            Hooks\Risky::class,
            Hooks\Skipped::class,
            Hooks\Successful::class,
            Hooks\Warning::class,
        ];

        $xpath = new \DOMXPath($xml);
        foreach ($expectedRegisteredHooks as $hook) {
            $matchingNodes = $xpath->evaluate(sprintf('/phpunit/extensions/extension[@class="%s"]', $hook));
            $this->assertInstanceOf(\DOMNodeList::class, $matchingNodes, 'Hook not registered: ' . $hook);
            $this->assertCount(1, $matchingNodes, 'Mismatch in matching nodes: ' . $xml->saveXML());
        }
    }

    public function validStubDirProvider(): ?\Generator
    {
        yield 'Default filename' => [$this->getStubPath() . 'StubbedXMLConfigs'];
        yield 'Fallback filename' => [$this->getStubPath() . 'StubbedXMLConfigs' . DIRECTORY_SEPARATOR . 'MissDefault'];
    }

    public function testGetFileFullPathWithMissConfigDefaultAndFallBack(): void
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs' . DIRECTORY_SEPARATOR . 'MissDefaultAndFallback';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(PHPUnitConfig::FALLBACK_CONFIG_FILE_NAME . ' does not exist');

        new PHPUnitConfig(
            $this->prophesize(TempDirectory::class)->reveal(),
            $this->prophesize(PHPUnitUtilXMLProxy::class)->reveal(),
            $dir
        );
    }

    public function testGetFileFullPathWithFileDoesNotExistWillThrowException(): void
    {
        $dir = $this->getStubPath() . 'PHPUnitJSONLogOutput';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(PHPUnitConfig::FALLBACK_CONFIG_FILE_NAME . ' does not exist');

        new PHPUnitConfig(
            $this->prophesize(TempDirectory::class)->reveal(),
            $this->prophesize(PHPUnitUtilXMLProxy::class)->reveal(),
            $dir
        );
    }

    public function testGetFileFullPathWithPathDoesNotExistWillThrowException(): void
    {
        $dir = $this->getStubPath() . 'foobar';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Config path/file provided is not valid');

        new PHPUnitConfig(
            $this->prophesize(TempDirectory::class)->reveal(),
            $this->prophesize(PHPUnitUtilXMLProxy::class)->reveal(),
            $dir
        );
    }
}
