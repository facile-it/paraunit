<?php
declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\LogPrinter;
use Paraunit\Process\TestWithCoverageCommandLine;
use Tests\BaseUnitTestCase;

/**
 * Class TestWithCoverageCliCommandTest
 * @package Tests\Unit\Process
 */
class TestWithCoverageCommandLineTest extends BaseUnitTestCase
{
    public function testGetExecutableWithoutDbg()
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

        $cli = new TestWithCoverageCommandLine($phpunit->reveal(), $phpDbg->reveal(), $tempFileNameFactory->reveal());

        $this->assertEquals('php path/to/phpunit', $cli->getExecutable());
    }

    public function testGetExecutableWithDbg()
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

        $cli = new TestWithCoverageCommandLine($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $this->assertEquals('/path/to/phpdbg', $cli->getExecutable());
    }

    public function testGetOptionsForWithoutDbg()
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()->willReturn('/path/to/phpunit.xml');
        $optionWithValue = new PHPUnitOption('optVal');
        $optionWithValue->setValue('value');
        $config->getPhpunitOptions()
            ->willReturn([
                new PHPUnitOption('opt', false),
                $optionWithValue
            ]);

        $phpDbg = $this->prophesize(PHPDbgBinFile::class);
        $phpDbg->isAvailable()
            ->shouldBeCalled()
            ->willReturn(false);
        $phpDbg->getPhpDbgBin()
            ->shouldNotBeCalled();
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $uniqueId = 'uniqueIdOfProcess';
        $fileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $fileNameFactory->getFilenameForLog($uniqueId)
            ->willReturn('/path/to/log.json');
        $fileNameFactory->getFilenameForCoverage($uniqueId)
            ->willReturn('/path/to/coverage.php');

        $cli = new TestWithCoverageCommandLine($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $this->assertEquals(
            '-c /path/to/phpunit.xml --printer="' . LogPrinter::class . '" --opt --optVal=value --coverage-php /path/to/coverage.php',
            $cli->getOptions($config->reveal(), $uniqueId)
        );
    }

    public function testGetOptionsForWithDbg()
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');
        $config->getPhpunitOptions()
            ->willReturn([]);

        $phpDbg = $this->prophesize(PHPDbgBinFile::class);
        $phpDbg->isAvailable()
            ->shouldBeCalled()
            ->willReturn(true);
        $phpDbg->getPhpDbgBin()
            ->shouldNotBeCalled();
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldBeCalled()
            ->willReturn('path/to/phpunit');
        $uniqueId = 'uniqueIdOfProcess';
        $fileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $fileNameFactory->getFilenameForLog($uniqueId)
            ->willReturn('/path/to/log.json');
        $fileNameFactory->getFilenameForCoverage($uniqueId)
            ->willReturn('/path/to/coverage.php');

        $cli = new TestWithCoverageCommandLine($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $this->assertEquals(
            '-qrr path/to/phpunit -c /path/to/phpunit.xml --printer="' . LogPrinter::class . '" --coverage-php /path/to/coverage.php',
            $cli->getOptions($config->reveal(), $uniqueId)
        );
    }
}
