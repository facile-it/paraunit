<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitBinFile;
use Tests\BaseUnitTestCase;

/**
 * Class PHPUnitBinFileTest
 * @package Tests\Unit\Configuration
 */
class PHPUnitBinFileTest extends BaseUnitTestCase
{
    public function testGetPhpunitBin()
    {
        $phpUnitBin = new PHPUnitBinFile();

        $this->assertStringStartsNotWith('php ', $phpUnitBin->getPhpUnitBin());
        $this->assertStringEndsWith(DIRECTORY_SEPARATOR . 'phpunit', $phpUnitBin->getPhpUnitBin());
    }
}
