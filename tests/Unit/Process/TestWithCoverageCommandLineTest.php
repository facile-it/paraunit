<?php

namespace Tests\Unit\Process;

use Paraunit\Process\TestWithCoverageCommandLine;
use Prophecy\Argument;

/**
 * Class TestWithCoverageCliCommandTest
 * @package Tests\Unit\Process
 */
class TestWithCoverageCommandLineTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExecutableWithoutDbg()
    {
        $phpDbg = $this->prophesize('Paraunit\Configuration\PHPDbgBinFile');
        $phpDbg->isAvailable()->shouldBeCalled()->willReturn(false);
        $phpDbg->getPhpDbgBin()->shouldNotBeCalled();
        $phpunit = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $phpunit->getPhpUnitBin()->shouldBeCalled()->willReturn('path/to/phpunit');
        $tempFileNameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');

        $cli = new TestWithCoverageCommandLine($phpunit->reveal(), $phpDbg->reveal(), $tempFileNameFactory->reveal());

        $this->assertEquals('path/to/phpunit', $cli->getExecutable());
    }

    public function testGetExecutableWithDbg()
    {
        $phpDbg = $this->prophesize('Paraunit\Configuration\PHPDbgBinFile');
        $phpDbg->isAvailable()->shouldBeCalled()->willReturn(true);
        $phpDbg->getPhpDbgBin()->shouldBeCalled()->willReturn('/path/to/phpdbg');
        $phpunit = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $phpunit->getPhpUnitBin()->shouldNotBeCalled();
        $fileNameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');

        $cli = new TestWithCoverageCommandLine($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $this->assertEquals('/path/to/phpdbg', $cli->getExecutable());
    }

    public function testGetOptionsForWithoutDbg()
    {
        $configFile = $this->prophesize('Paraunit\Configuration\PHPUnitConfigFile');
        $configFile->getFileFullPath()->willReturn('/path/to/phpunit.xml');
        $phpDbg = $this->prophesize('Paraunit\Configuration\PHPDbgBinFile');
        $phpDbg->isAvailable()->shouldBeCalled()->willReturn(false);
        $phpDbg->getPhpDbgBin()->shouldNotBeCalled();
        $phpunit = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $uniqueId = 'uniqueIdOfProcess';
        $fileNameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $fileNameFactory->getFilenameForLog($uniqueId)->willReturn('/path/to/log.json');
        $fileNameFactory->getFilenameForCoverage($uniqueId)->willReturn('/path/to/coverage.php');

        $cli = new TestWithCoverageCommandLine($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $this->assertEquals(
            '-c /path/to/phpunit.xml --log-json /path/to/log.json --coverage-php /path/to/coverage.php',
            $cli->getOptions($configFile->reveal(), $uniqueId)
        );
    }

    public function testGetOptionsForWithDbg()
    {
        $configFile = $this->prophesize('Paraunit\Configuration\PHPUnitConfigFile');
        $configFile->getFileFullPath()->willReturn('/path/to/phpunit.xml');
        $phpDbg = $this->prophesize('Paraunit\Configuration\PHPDbgBinFile');
        $phpDbg->isAvailable()->shouldBeCalled()->willReturn(true);
        $phpDbg->getPhpDbgBin()->shouldNotBeCalled();
        $phpunit = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $phpunit->getPhpUnitBin()->shouldBeCalled()->willReturn('path/to/phpunit');
        $uniqueId = 'uniqueIdOfProcess';
        $fileNameFactory = $this->prophesize('Paraunit\Configuration\TempFilenameFactory');
        $fileNameFactory->getFilenameForLog($uniqueId)->willReturn('/path/to/log.json');
        $fileNameFactory->getFilenameForCoverage($uniqueId)->willReturn('/path/to/coverage.php');

        $cli = new TestWithCoverageCommandLine($phpunit->reveal(), $phpDbg->reveal(), $fileNameFactory->reveal());

        $this->assertEquals(
            '-qrr path/to/phpunit -c /path/to/phpunit.xml --log-json /path/to/log.json --coverage-php /path/to/coverage.php',
            $cli->getOptions($configFile->reveal(), $uniqueId)
        );
    }
}
