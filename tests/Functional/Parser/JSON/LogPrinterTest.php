<?php

namespace Tests\Functional\Parser\JSON;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\LogPrinter;
use Tests\BaseFunctionalTestCase;

/**
 * Class LogPrinterTest
 * @package Tests\Functional\Parser\JSON
 */
class LogPrinterTest extends BaseFunctionalTestCase
{
    public function testWrite()
    {
        $testName = get_class();
        $testSuite = $this->prophesize('\PHPUnit_Framework_TestSuite');
        $testSuite->getName()
            ->willReturn($testName);
        $testSuite->count()
            ->willReturn(1);
        
        $printer = new LogPrinter();
        
        $printer->startTestSuite($testSuite->reveal());
        
        $logFilename = $this->getLogFilenameForTest(__FILE__);
        $this->assertFileExists($logFilename);
        
        $content = file_get_contents($logFilename);
        unlink($logFilename);
        $this->assertJson($content);
        $decodedJson = json_decode($content, true);
        $this->assertEquals(array('event' => 'suiteStart', 'suite' => $testName, 'tests' => 1), $decodedJson);
    }

    /**
     * @param string $testFilename
     * @return string
     */
    private function getLogFilenameForTest($testFilename)
    {
        /** @var TempFilenameFactory $filenameFactory */
        $filenameFactory = $this->container->get('paraunit.configuration.temp_filename_factory');
        
        return $filenameFactory->getFilenameForLog(md5($testFilename));
    }
}
