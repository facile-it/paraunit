<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Parser\JSON\LogPrinter;
use Paraunit\Parser\JSON\LogPrinterStderr;
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

        $cli = new CommandLine($phpunit->reveal());
        $options = $cli->getOptions($config->reveal());
        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--printer=' . LogPrinter::class, $options);
        $this->assertContains('--opt', $options);
        $this->assertContains('--optVal=value', $options);
    }

    public function testGetOptionsStderr(): void
    {
        $stderrOption = new PHPUnitOption('stderr', false);

        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getPhpunitOption('stderr')
            ->willReturn($stderrOption);

        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');

        $config->getPhpunitOptions()->willReturn([
            $stderrOption,
        ]);

        $phpunit = $this->prophesize(PHPUnitBinFile::class);

        $cli = new CommandLine($phpunit->reveal());
        $options = $cli->getOptions($config->reveal());
        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--printer=' . LogPrinterStderr::class, $options);
        $this->assertContains('--stderr', $options);
    }
}
