<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitConfig;
use Tests\BaseUnitTestCase;

class PHPUnitConfigTest extends BaseUnitTestCase
{
    public function testGetBaseDirectoryIsNotLazy(): void
    {
        $config = new PHPUnitConfig('');

        $directoryPath = $config->getBaseDirectory();
        $this->assertNotEquals('', $directoryPath);
        $this->assertNotEquals('/', $directoryPath);
        $this->assertNotEquals('C:\\', $directoryPath);
    }

    public function testGetFileFullPathWithDirAndUseDefaultFileName(): void
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs';
        $configurationFile = $dir . DIRECTORY_SEPARATOR . 'phpunit.xml.dist';

        $config = new PHPUnitConfig($dir);

        $this->assertEquals($configurationFile, $config->getFileFullPath());
    }

    public function testGetFileFullPathWithMissConfigDefault(): void
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs' . DIRECTORY_SEPARATOR . 'MissDefault';
        $config = new PHPUnitConfig($dir);

        $configurationFile = $dir . DIRECTORY_SEPARATOR . 'phpunit.xml.dist';
        $this->assertEquals($configurationFile, $config->getFileFullPath());
    }

    public function testGetFileFullPathWithMissConfigDefaultAndFallBack(): void
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs' . DIRECTORY_SEPARATOR . 'MissDefaultAndFallback';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(PHPUnitConfig::FALLBACK_CONFIG_FILE_NAME . ' does not exist');

        new PHPUnitConfig($dir);
    }

    public function testGetFileFullPathWithFileDoesNotExistWillThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(PHPUnitConfig::FALLBACK_CONFIG_FILE_NAME . ' does not exist');

        new PHPUnitConfig(__DIR__);
    }

    public function testGetFileFullPathWithPathDoesNotExistWillThrowException(): void
    {
        $dir = $this->getStubPath() . 'foobar';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Config path/file provided is not valid');

        new PHPUnitConfig($dir);
    }

    /**
     * @dataProvider configWithExtensionDataProvider
     */
    public function testIsParaunitExtensionRegistered(bool $expectedResult, string $configContent): void
    {
        $configFile = uniqid(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'paraunit-test-config-');
        $this->assertNotFalse(file_put_contents($configFile, $configContent), 'Failed preparing mocked config');

        $config = new PHPUnitConfig($configFile);

        $this->assertSame($expectedResult, $config->isParaunitExtensionRegistered());
    }

    /**
     * @return array<string, array{bool, string}>
     */
    public static function configWithExtensionDataProvider(): array
    {
        return [
            'no extensions section' => [
                false,
                '<?xml version="1.0"?><phpunit></phpunit>',
            ],
            'no Paraunit extension' => [
                false,
                '<?xml version="1.0"?><phpunit><extensions><bootstrap class="Foo" /></extensions></phpunit>',
            ],
            'Paraunit extension registered' => [
                true,
                '<?xml version="1.0"?><phpunit><extensions><bootstrap class="\Paraunit\Configuration\ParaunitExtension" /></extensions></phpunit>',
            ],
            'Paraunit extension registered but not first one' => [
                true,
                '<?xml version="1.0"?><phpunit><extensions><bootstrap class="\Another\Extension" /><bootstrap class="\Paraunit\Configuration\ParaunitExtension" /></extensions></phpunit>',
            ],
            'Paraunit extension registered with no starting backslash' => [
                true,
                '<?xml version="1.0"?><phpunit><extensions><bootstrap class="Paraunit\Configuration\ParaunitExtension" /></extensions></phpunit>',
            ],
        ];
    }
}
