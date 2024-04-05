<?php

declare(strict_types=1);

namespace Tests\Functional\Configuration;

use Paraunit\Configuration\PHPDbgBinFile;
use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use Tests\BaseFunctionalTestCase;

#[RequiresOperatingSystemFamily('Linux')]
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
        $this->assertStringNotContainsString(' ', $bin->getPhpDbgBin());
        $this->assertStringNotContainsString("\n", $bin->getPhpDbgBin());
    }
}
