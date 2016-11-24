<?php


namespace Tests\Functional\Configuration;

use Paraunit\Configuration\PHPDbgBinFile;
use Tests\BaseFunctionalTestCase;

/**
 * Class PHPDbgBinFileTest
 * @package Tests\Functional\Configuration
 */
class PHPDbgBinFileTest extends BaseFunctionalTestCase
{
    public function testIsAvailable()
    {
        $bin = new PHPDbgBinFile();

        $this->assertSame($this->shouldPHPDbgDriverBeAvailable(), $bin->isAvailable());
    }

    public function testGetPhpDbgBin()
    {
        if (! $this->shouldPHPDbgDriverBeAvailable()) {
            $this->markTestSkipped('PHPDbg coverage driver not available for PHP < 7.0');
        }

        $bin = new PHPDbgBinFile();

        $this->assertStringEndsWith('phpdbg', $bin->getPhpDbgBin());
        $this->assertNotContains(' ', $bin->getPhpDbgBin());
        $this->assertNotContains("\n", $bin->getPhpDbgBin());
    }

    private function shouldPHPDbgDriverBeAvailable()
    {
        return version_compare('7', PHP_VERSION, '<');
    }
}
