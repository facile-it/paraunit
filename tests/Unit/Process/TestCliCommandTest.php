<?php

namespace Tests\Unit\Process;

use Paraunit\Configuration\TempFileNameFactory;
use Paraunit\Process\TestCliCommand;
use Prophecy\Argument;

/**
 * Class TestCliCommandTest
 * @package Tests\Unit\Process
 */
class TestCliCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExecutable()
    {
        $phpunit = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $phpunit->getPhpUnitBin()->willReturn('path/to/phpunit');
        $fileNameFactory = $this->prophesize('Paraunit\Configuration\TempFileNameFactory');

        $cli = new TestCliCommand($phpunit->reveal(), $fileNameFactory->reveal());

        $this->assertEquals('path/to/phpunit', $cli->getExecutable());
    }

    public function testGetOptionsFor()
    {
        $configFile = $this->prophesize('Paraunit\Configuration\PHPUnitConfigFile');
        $configFile->getFileFullPath()->willReturn('/path/to/phpunit.xml');
        $phpunit = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $uniqueId = 'uniqueIdOfProcess';
        $fileNameFactory = $this->prophesize('Paraunit\Configuration\TempFileNameFactory');
        $fileNameFactory->getFilenameForLog($uniqueId)->willReturn('/path/to/log.json');

        $cli = new TestCliCommand($phpunit->reveal(), $fileNameFactory->reveal());

        $this->assertEquals(
            '-c /path/to/phpunit.xml --log-json /path/to/log.json',
            $cli->getOptions($configFile->reveal(), $uniqueId)
        );
    }
}
