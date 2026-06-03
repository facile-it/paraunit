<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Coverage\CoverageCacheWarmer;
use Paraunit\Process\CommandLine;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\BaseUnitTestCase;

class CoverageCacheWarmerTest extends BaseUnitTestCase
{
    public function testWarmCacheThrowsWhenWarmupProcessFails(): void
    {
        $commandLine = $this->prophesize(CommandLine::class);
        $commandLine->getExecutable()
            ->willReturn(['exit 1 &&']);

        $phpunitConfig = $this->prophesize(PHPUnitConfig::class);
        $phpunitConfig->isCoverageCacheEnabled()
            ->willReturn(true);

        $output = new BufferedOutput();

        $warmer = new CoverageCacheWarmer(
            $commandLine->reveal(),
            $phpunitConfig->reveal(),
            $output,
        );

        try {
            $warmer->warmCache();
            $this->fail('Expected RuntimeException when coverage cache warmup command fails');
        } catch (\RuntimeException $e) {
            $this->assertSame('PHPUnit coverage cache warmup failed', $e->getMessage());
        }

        $buffer = $output->fetch();
        $this->assertStringContainsString('Warming up coverage cache...', $buffer);
        $this->assertStringContainsString('ERROR: PHPUnit coverage cache warmup failed', $buffer);
    }
}
