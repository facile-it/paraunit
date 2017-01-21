<?php

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\LogPrinter;
use Tests\BaseUnitTestCase;

/**
 * Class LogPrinterTest
 * @package Tests\Unit\Parser\JSON
 */
class LogPrinterTest extends BaseUnitTestCase
{
    private $prettyPrint;

    /**
     * LogPrinterTest constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (defined('JSON_PRETTY_PRINT')) {
            $this->prettyPrint = JSON_PRETTY_PRINT;
        }
    }


    public function testStartTestSuite()
    {
        $this->createPrinterAndStartTestSuite();

        $this->assertEquals($this->encodeWithStartTestSuite(), $this->getLogContent());
    }

    public function testAddError()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize('\PHPUnit_Framework_Test')->reveal();

        $printer->startTest($test);
        $printer->addError($test, new \Exception('Exception message'), 1);
        $line = __LINE__ - 1;

        $expectedContent = $this->encodeWithStartTestSuite(array(
            array(
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ),
            array(
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'error',
                'time' => 1,
                'trace' => array(
                    array(
                        'file' => __FILE__,
                        'line' => $line,
                    ),
                ),
                'message' => 'Exception: Exception message' . PHP_EOL,
                'output' => '',
            ),
        ));
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddWarning()
    {
        $phpunitVersion = new \PHPUnit_Runner_Version();

        if (! preg_match('/^5\./', $phpunitVersion->id())) {
            $this->markTestSkipped('PHPUnit < 5 in this env, warnings are not present.');
        }

        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize('\PHPUnit_Framework_Test')->reveal();
        // has final methods, cannot be mocked
        $warning = new \PHPUnit_Framework_Warning('Warning message', null, new \Exception());
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addWarning($test, $warning, 1);

        $expectedContent = $this->encodeWithStartTestSuite(array(
            array(
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ),
            array(
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'warning',
                'time' => 1,
                'trace' => array(
                    array(
                        'file' => __FILE__,
                        'line' => $line,
                    ),
                ),
                'message' => 'Warning message' . PHP_EOL,
                'output' => '',
            ),
        ));
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddFailure()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize('\PHPUnit_Framework_Test')->reveal();
        // has final methods, cannot be mocked
        $failure = new \PHPUnit_Framework_AssertionFailedError('Failure message', null, new \Exception());
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addFailure($test, $failure, 1);

        $expectedContent = $this->encodeWithStartTestSuite(array(
            array(
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ),
            array(
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'fail',
                'time' => 1,
                'trace' => array(
                    array(
                        'file' => __FILE__,
                        'line' => $line,
                    ),
                ),
                'message' => 'Failure message' . PHP_EOL,
                'output' => '',
            ),
        ));
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddIncompleteTest()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize('\PHPUnit_Framework_Test')->reveal();
        // has final methods, cannot be mocked
        $failure = new \Exception('Incomplete message');
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addIncompleteTest($test, $failure, 1);

        $expectedContent = $this->encodeWithStartTestSuite(array(
            array(
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ),
            array(
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'error',
                'time' => 1,
                'trace' => array(
                    array(
                        'file' => __FILE__,
                        'line' => $line,
                    ),
                ),
                'message' => 'Incomplete Test: Incomplete message',
                'output' => '',
            ),
        ));
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddRiskyTest()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize('\PHPUnit_Framework_Test')->reveal();
        // has final methods, cannot be mocked
        $failure = new \Exception('Risky message');
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addRiskyTest($test, $failure, 1);

        $expectedContent = $this->encodeWithStartTestSuite(array(
            array(
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ),
            array(
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'error',
                'time' => 1,
                'trace' => array(
                    array(
                        'file' => __FILE__,
                        'line' => $line,
                    ),
                ),
                'message' => 'Risky Test: Risky message',
                'output' => '',
            ),
        ));
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddSkippedTest()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize('\PHPUnit_Framework_Test')->reveal();
        // has final methods, cannot be mocked
        $failure = new \Exception('Skipped message');
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addSkippedTest($test, $failure, 1);

        $expectedContent = $this->encodeWithStartTestSuite(array(
            array(
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ),
            array(
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'error',
                'time' => 1,
                'trace' => array(
                    array(
                        'file' => __FILE__,
                        'line' => $line,
                    ),
                ),
                'message' => 'Skipped Test: Skipped message',
                'output' => '',
            ),
        ));
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testEndTest()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize('\PHPUnit_Framework_Test')->reveal();

        $printer->startTest($test);
        $printer->endTest($test, 1);

        $expectedContent = $this->encodeWithStartTestSuite(array(
            array(
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ),
            array(
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'pass',
                'time' => 1,
                'trace' => array(),
                'message' => '',
                'output' => '',
            ),
        ));
        $this->assertEquals($expectedContent, $this->getLogContent());

        $printer->endTestSuite($this->prophesize('\PHPUnit_Framework_TestSuite')->reveal());

        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    private function createPrinterAndStartTestSuite()
    {
        $printer = new LogPrinter(sys_get_temp_dir());
        $testSuite = $this->prophesize('\PHPUnit_Framework_TestSuite');
        $testSuite->getName()
            ->willReturn(get_class($this));
        $testSuite->count()
            ->willReturn(1);

        $printer->startTestSuite($testSuite->reveal());

        return $printer;
    }

    private function getLogContent()
    {
        $logFilename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(__FILE__) . '.json.log';
        $this->assertFileExists($logFilename, 'Log file missing! Maybe you called this method too early?');

        return file_get_contents($logFilename);
    }

    /**
     * @param array $data
     * @return string
     */
    private function encodeWithStartTestSuite(array $data = array())
    {
        $logElements = array($this->getStartTestSuiteLog());
        foreach ($data as $datum) {
            $logElements[] = $datum;
        }

        $result = '';
        foreach ($logElements as $logElement) {
            $result .= json_encode($logElement, $this->prettyPrint);
        }

        return $result;
    }

    private function getStartTestSuiteLog()
    {
        return array(
            'event' => 'suiteStart',
            'suite' => get_class($this),
            'tests' => 1,
        );
    }
}
