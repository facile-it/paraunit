<?php

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Process\TestCommandLine;

/**
 * Class TestCliCommandTest
 * @package Tests\Unit\Process
 */
class TestCommandLineTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExecutable()
    {
        $phpunit = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $phpunit->getPhpUnitBin()->willReturn('path/to/phpunit');
        $fileNameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');

        $cli = new TestCommandLine($phpunit->reveal(), $fileNameFactory->reveal());

        $this->assertEquals('php path/to/phpunit', $cli->getExecutable());
    }

    public function testGetOptionsFor()
    {
        $config = $this->prophesize('Paraunit\Configuration\PHPUnitConfig');
        $config->getFileFullPath()->willReturn('/path/to/phpunit.xml');
        
        $optionWithValue = new PHPUnitOption('optVal');
        $optionWithValue->setValue('value');
        $config->getPhpunitOptions()->willReturn(array(
            new PHPUnitOption('opt', false),
            $optionWithValue
        ));
        
        $phpunit = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $uniqueId = 'uniqueIdOfProcess';
        $fileNameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $fileNameFactory->getFilenameForLog($uniqueId)->willReturn('/path/to/log.json');

        $cli = new TestCommandLine($phpunit->reveal(), $fileNameFactory->reveal());

        $this->assertEquals(
            '-c /path/to/phpunit.xml --printer="Paraunit\\Parser\\JSON\\LogPrinter" --opt --optVal=value',
            $cli->getOptions($config->reveal(), $uniqueId)
        );
    }
}
