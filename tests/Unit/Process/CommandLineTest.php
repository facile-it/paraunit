<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

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

        $cli = new CommandLine($phpunit->reveal());

        $this->assertEquals(['php', 'path/to/phpunit'], $cli->getExecutable());
    }

    public function testGetOptionsFor(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');

        $optionWithValue = new PHPUnitOption('optVal');
        $optionWithValue->setValue('value');
        $config->getPhpunitOptions()->willReturn([
            new PHPUnitOption('opt', false),
            $optionWithValue,
        ]);

        $phpunit = $this->prophesize(PHPUnitBinFile::class);

        $cli = new CommandLine($phpunit->reveal());
        $options = $cli->getOptions($config->reveal());
        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--opt', $options);
        $this->assertContains('--optVal=value', $options);

        $extensions = array_filter($options, static function (string $a) {
            return 0 === strpos($a, '--extensions');
        });
        $this->assertCount(1, $extensions, 'Missing --extensions from options');
        $registeredExtensions = array_pop($extensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Error::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Failure::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Incomplete::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Risky::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Skipped::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Successful::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Warning::class, $registeredExtensions);
    }
}
