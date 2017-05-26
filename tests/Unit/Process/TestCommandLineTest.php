<?php
declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\LogPrinter;
use Paraunit\Process\TestCommandLine;
use Tests\BaseUnitTestCase;

/**
 * Class TestCliCommandTest
 * @package Tests\Unit\Process
 */
class TestCommandLineTest extends BaseUnitTestCase
{
    public function testGetExecutable()
    {
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()->willReturn('path/to/phpunit');
        $fileNameFactory = $this->prophesize(TempFilenameFactory::class);

        $cli = new TestCommandLine($phpunit->reveal(), $fileNameFactory->reveal());

        $this->assertEquals('php path/to/phpunit', $cli->getExecutable());
    }

    public function testGetOptionsFor()
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');

        $optionWithValue = new PHPUnitOption('optVal');
        $optionWithValue->setValue('value');
        $config->getPhpunitOptions()
            ->willReturn([
                new PHPUnitOption('opt', false),
                $optionWithValue
            ]);

        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $uniqueId = 'uniqueIdOfProcess';
        $fileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $fileNameFactory->getFilenameForLog($uniqueId)
            ->willReturn('/path/to/log.json');

        $cli = new TestCommandLine($phpunit->reveal(), $fileNameFactory->reveal());

        $this->assertEquals(
            '-c /path/to/phpunit.xml --printer="' . LogPrinter::class . '" --opt --optVal=value',
            $cli->getOptions($config->reveal(), $uniqueId)
        );
    }
}
