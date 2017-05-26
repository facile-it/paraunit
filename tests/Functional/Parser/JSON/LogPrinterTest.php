<?php
declare(strict_types=1);

namespace Tests\Functional\Parser\JSON;

use Paraunit\Configuration\StaticOutputPath;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\LogPrinter;
use PHPUnit\Framework\TestSuite;
use Tests\BaseFunctionalTestCase;

/**
 * Class LogPrinterTest
 * @package Tests\Functional\Parser\JSON
 */
class LogPrinterTest extends BaseFunctionalTestCase
{
    public function testLogFilenameMatches()
    {
        $testName = __CLASS__;
        $testSuite = $this->prophesize(TestSuite::class);
        $testSuite->getName()
            ->willReturn($testName);
        $testSuite->count()
            ->willReturn(1);

        $dir = sys_get_temp_dir();
        new StaticOutputPath($dir);
        $printer = new LogPrinter();

        $reflectionMethod = new \ReflectionMethod($printer, 'getLogFilename');
        $reflectionMethod->setAccessible(true);
        $this->assertEquals(
            $dir . DIRECTORY_SEPARATOR . md5(__FILE__) . '.json.log',
            $reflectionMethod->invoke($printer, $testSuite->reveal())
        );
    }

    public function testWrite()
    {
        $testName = __CLASS__;
        $testSuite = $this->prophesize(TestSuite::class);
        $testSuite->getName()
            ->willReturn($testName);
        $testSuite->count()
            ->willReturn(1);
        $logFilename = $this->getLogFilenameForTest(__FILE__);

        new StaticOutputPath(dirname($logFilename));
        $printer = new LogPrinter();

        $printer->startTestSuite($testSuite->reveal());

        $this->assertFileExists($logFilename);

        $content = file_get_contents($logFilename);
        $this->assertJson($content);
        $decodedJson = json_decode($content, true);
        $this->assertEquals(['event' => 'suiteStart', 'suite' => $testName, 'tests' => 1], $decodedJson);
    }

    private function getLogFilenameForTest(string $testFilename): string
    {
        /** @var TempFilenameFactory $filenameFactory */
        $filenameFactory = $this->container->get('paraunit.configuration.temp_filename_factory');

        return $filenameFactory->getFilenameForLog(md5($testFilename));
    }
}
