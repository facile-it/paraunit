<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitBinFile;
use Tests\BaseUnitTestCase;

class PHPUnitBinFileTest extends BaseUnitTestCase
{
    public function testGetPhpunitBin()
    {
        $phpUnitBin = new PHPUnitBinFile();

        $this->assertStringStartsNotWith('php ', $phpUnitBin->getPhpUnitBin());
        $this->assertStringEndsWith(DIRECTORY_SEPARATOR . 'phpunit', $phpUnitBin->getPhpUnitBin());
    }
}
