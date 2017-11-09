<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Parser\JSON\LogPrinter;
use Paraunit\Process\CommandLine;
use Tests\BaseUnitTestCase;

/**
 * Class TestCliCommandTest
 * @package Tests\Unit\Process
 */
class CommandLineTest extends BaseUnitTestCase
{
    public function testGetExecutable()
    {
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()->willReturn('path/to/phpunit');

        $cli = new CommandLine($phpunit->reveal());

        $this->assertEquals(['php', 'path/to/phpunit'], $cli->getExecutable());
    }

    public function testGetOptionsFor()
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
        $this->assertInternalType('array', $options, 'Expecting an array, got ' . gettype($options));
        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--printer=' . LogPrinter::class, $options);
        $this->assertContains('--opt', $options);
        $this->assertContains('--optVal=value', $options);
    }
}
