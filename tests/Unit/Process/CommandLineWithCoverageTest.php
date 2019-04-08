<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\LogPrinter;
use Paraunit\Process\CommandLineWithCoverage;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class CommandLineWithCoverageTest extends BaseUnitTestCase
{
    public function testGetExecutableWithoutDbg(): void
    {
        $phpDbg = $this->prophesize(PHPDbgBinFile::class);
        $phpDbg->isAvailable()
            ->shouldBeCalled()
            ->willReturn(false);
        $phpDbg->getPhpDbgBin()
            ->shouldNotBeCalled();
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldBeCalled()
            ->willReturn('path/to/phpunit');
        $tempFileNameFactory = $this->prophesize(TempFilenameFactory::class);

        $cli = new CommandLineWithCoverage($phpunit->reveal(), $phpDbg->reveal(), $tempFileNameFactory->reveal());

        $this->assertEquals(['php', 'path/to/phpunit'], $cli->getExecutable());
    }

    public function testGetExecutableWithDbg(): void
    {
        $phpDbg = $this->prophesize(PHPDbgBinFile::class);
        $phpDbg->isAvailable()
            ->shouldBeCalled()
            ->willReturn(true);
        $phpDbg->getPhpDbgBin()
            ->shouldBeCalled()
            ->willReturn('/path/to/phpdbg');
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldNotBeCalled();
        $fileNameFactory = $this->prophesize(TempFilenameFactory::class);

        $cli = new CommandLineWithCoverage($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $this->assertEquals(['/path/to/phpdbg'], $cli->getExecutable());
    }

    public function testGetOptionsForWithoutDbg(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()->willReturn('/path/to/phpunit.xml');
        $optionWithValue = new PHPUnitOption('optVal');
        $optionWithValue->setValue('value');
        $config->getPhpunitOptions()
            ->willReturn([
                new PHPUnitOption('opt', false),
                $optionWithValue,
            ]);

        $phpDbg = $this->prophesize(PHPDbgBinFile::class);
        $phpDbg->isAvailable()
            ->shouldBeCalled()
            ->willReturn(false);
        $phpDbg->getPhpDbgBin()
            ->shouldNotBeCalled();
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $fileNameFactory = $this->prophesize(TempFilenameFactory::class);

        $cli = new CommandLineWithCoverage($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $options = $cli->getOptions($config->reveal());

        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--printer=' . LogPrinter::class, $options);
        $this->assertContains('--opt', $options);
        $this->assertContains('--optVal=value', $options);
    }

    public function testGetOptionsForWithDbg(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');
        $config->getPhpunitOptions()
            ->willReturn([
                new PHPUnitOption('opt', false),
            ]);

        $phpDbg = $this->prophesize(PHPDbgBinFile::class);
        $phpDbg->isAvailable()->shouldBeCalled()->willReturn(true);
        $phpDbg->getPhpDbgBin()->shouldNotBeCalled();
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()->shouldBeCalled()->willReturn('path/to/phpunit');
        $fileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $cli = new CommandLineWithCoverage($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $options = $cli->getOptions($config->reveal());

        $this->assertContains('-qrr', $options);
        $this->assertEquals('-qrr', $options[0], '-qrr option needs to be the first one!');
        $this->assertContains('path/to/phpunit', $options);
        $this->assertEquals('path/to/phpunit', $options[1], 'PHPUnit bin path must follow the -qrr option');
        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--printer=' . LogPrinter::class, $options);
        $this->assertContains('--opt', $options);
    }

    public function testGetSpecificOptions(): void
    {
        $testFilename = 'TestTest.php';
        $process = new StubbedParaunitProcess($testFilename);
        $uniqueId = $process->getUniqueId();
        $phpDbg = $this->prophesize(PHPDbgBinFile::class);
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $fileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $fileNameFactory->getFilenameForCoverage($uniqueId)
            ->willReturn('/path/to/coverage.php');

        $cli = new CommandLineWithCoverage($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $options = $cli->getSpecificOptions($testFilename);

        $this->assertContains('--coverage-php=/path/to/coverage.php', $options);
    }
}
