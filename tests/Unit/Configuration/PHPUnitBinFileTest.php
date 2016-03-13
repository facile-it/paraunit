<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitBinFile;

/**
 * Class PHPUnitBinFileTest
 * @package Tests\Unit\Configuration
 */
class PHPUnitBinFileTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPhpunitBin()
    {
        $phpUnitBin = new PHPUnitBinFile();

        $this->assertStringEndsWith(DIRECTORY_SEPARATOR . 'phpunit', $phpUnitBin->getPhpUnitBin());
    }
}
