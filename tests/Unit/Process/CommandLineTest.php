<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Parser\JSON\TestHook as Hooks;
use Paraunit\Process\CommandLine;
use Tests\BaseUnitTestCase;

class CommandLineTest extends BaseUnitTestCase
{
    public function testGetExecutable(): void
    {
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()->willReturn('path/to/phpunit');
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->shouldNotBeCalled();

        $cli = new CommandLine($phpunit->reveal(), $chunkSize->reveal());

        $this->assertEquals(['php', 'path/to/phpunit'], $cli->getExecutable());
    }

    public function testGetOptionsFor(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getPhpunitOption('stderr')
            ->willReturn(null);

        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');

        $optionWithValue = new PHPUnitOption('optVal');
        $optionWithValue->setValue('value');
        $config->getPhpunitOptions()->willReturn([
            new PHPUnitOption('opt', false),
            $optionWithValue,
        ]);

        $phpunit = $this->prophesize(PHPUnitBinFile::class);

        $cli = new CommandLine($phpunit->reveal(), $this->mockChunkSize(false));
        $options = $cli->getOptions($config->reveal());
        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--opt', $options);
        $this->assertContains('--optVal=value', $options);

        $extensions = array_filter($options, static function (string $a) {
            return 0 === strpos($a, '--extensions');
        });
        $this->assertCount(1, $extensions, 'Missing --extensions from options');
        $registeredExtensions = array_pop($extensions);
        $this->assertNotNull($registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\BeforeTest::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Error::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Failure::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Incomplete::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Risky::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Skipped::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Successful::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Warning::class, $registeredExtensions);
    }

    public function testGetOptionsChunkedNotContainsConfiguration(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getPhpunitOption('stderr')
            ->willReturn(null);

        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');

        $config->getPhpunitOptions()
            ->willReturn([]);

        $phpunit = $this->prophesize(PHPUnitBinFile::class);

        $cli = new CommandLine($phpunit->reveal(), $this->mockChunkSize(true));
        foreach ($cli->getOptions($config->reveal()) as $option) {
            $this->assertStringStartsNotWith('--configuration', $option);
        }
    }

    public function testGetOptionsChunkedNotContainsTestsuite(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getPhpunitOption('stderr')
            ->willReturn(null);

        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');

        $incompatibleOption = new PHPUnitOption('testsuite');
        $incompatibleOption->setValue('foo');
        $config->getPhpunitOptions()
            ->willReturn([$incompatibleOption]);

        $phpunit = $this->prophesize(PHPUnitBinFile::class);

        $cli = new CommandLine($phpunit->reveal(), $this->mockChunkSize(true));
        foreach ($cli->getOptions($config->reveal()) as $option) {
            $this->assertStringStartsNotWith('--testsuite', $option);
        }
    }

    public function testGetOptionsNotChunkedContainsTestsuite(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getPhpunitOption('stderr')
            ->willReturn(null);

        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');

        $incompatibleOption = new PHPUnitOption('testsuite');
        $incompatibleOption->setValue('foo');
        $config->getPhpunitOptions()
            ->willReturn([$incompatibleOption]);

        $phpunit = $this->prophesize(PHPUnitBinFile::class);

        $cli = new CommandLine($phpunit->reveal(), $this->mockChunkSize(false));
        $this->assertContains('--testsuite=foo', $cli->getOptions($config->reveal()));
    }

    private function mockChunkSize(bool $enabled): ChunkSize
    {
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->shouldBeCalled()
            ->willReturn($enabled);

        return $chunkSize->reveal();
    }
}
