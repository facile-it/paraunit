<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
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
        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');

        $phpunit = $this->prophesize(PHPUnitBinFile::class);

        $cli = new CommandLine($phpunit->reveal(), $this->mockChunkSize(false));
        $options = $cli->getOptions($config->reveal());
        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);

        $extensions = array_filter($options, static fn (string $a): bool => str_starts_with($a, '--extensions'));
        $this->assertCount(0, $extensions, '--extensions should no longer be used');
    }

    public function testGetOptionsChunkedNotContainsConfiguration(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');

        $phpunit = $this->prophesize(PHPUnitBinFile::class);

        $cli = new CommandLine($phpunit->reveal(), $this->mockChunkSize(true));
        $options = $cli->getOptions($config->reveal());
        $this->assertNotContains('--configuration=/path/to/phpunit.xml', $options);
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
