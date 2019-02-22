<?php

declare(strict_types=1);

namespace Tests\Functional\Configuration;

use Paraunit\Configuration\PHPDbgBinFile;
use Tests\BaseFunctionalTestCase;

class PHPDbgBinFileTest extends BaseFunctionalTestCase
{
    public function testIsAvailable(): void
    {
        $bin = new PHPDbgBinFile();

        $this->assertTrue($bin->isAvailable());
    }

    public function testGetPhpDbgBin(): void
    {
        $bin = new PHPDbgBinFile();

        $this->assertStringEndsWith('phpdbg', $bin->getPhpDbgBin());
        $this->assertNotContains(' ', $bin->getPhpDbgBin());
        $this->assertNotContains("\n", $bin->getPhpDbgBin());
    }
}
