<?php
declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Parser\JSON\LogPrinter;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use Tests\BaseUnitTestCase;

/**
 * Class LogPrinterTest
 * @package Tests\Unit\Parser\JSON
 */
class LogPrinterTest extends BaseUnitTestCase
{
    public function testStartTestSuite()
    {
        $this->createPrinterAndStartTestSuite();

        $this->assertEquals($this->encodeWithStartTestSuite(), $this->getLogContent());
    }

    public function testAddError()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize(Test::class)->reveal();

        $printer->startTest($test);
        $printer->addError($test, new \Exception('Exception message'), 1);
        $line = __LINE__ - 1;

        $expectedContent = $this->encodeWithStartTestSuite([
            [
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ],
            [
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'error',
                'time' => 1,
                'trace' => [
                    [
                        'file' => __FILE__,
                        'line' => $line,
                    ],
                ],
                'message' => 'Exception: Exception message' . "\n",
                'output' => '',
            ],
        ]);
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddWarning()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize(Test::class)->reveal();
        // has final methods, cannot be mocked
        $warning = new Warning('Warning message', null, new \Exception());
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addWarning($test, $warning, 1);

        $expectedContent = $this->encodeWithStartTestSuite([
            [
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ],
            [
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'warning',
                'time' => 1,
                'trace' => [
                    [
                        'file' => __FILE__,
                        'line' => $line,
                    ],
                ],
                'message' => 'Warning message' . "\n",
                'output' => '',
            ],
        ]);
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddFailure()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize(Test::class)->reveal();
        // has final methods, cannot be mocked
        $failure = new AssertionFailedError('Failure message', null, new \Exception());
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addFailure($test, $failure, 1);

        $expectedContent = $this->encodeWithStartTestSuite([
            [
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ],
            [
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'fail',
                'time' => 1,
                'trace' => [
                    [
                        'file' => __FILE__,
                        'line' => $line,
                    ],
                ],
                'message' => 'Failure message' . "\n",
                'output' => '',
            ],
        ]);
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddIncompleteTest()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize(Test::class)->reveal();
        // has final methods, cannot be mocked
        $failure = new \Exception('Incomplete message');
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addIncompleteTest($test, $failure, 1);

        $expectedContent = $this->encodeWithStartTestSuite([
            [
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ],
            [
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'error',
                'time' => 1,
                'trace' => [
                    [
                        'file' => __FILE__,
                        'line' => $line,
                    ],
                ],
                'message' => 'Incomplete Test: Incomplete message',
                'output' => '',
            ],
        ]);
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddRiskyTest()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize(Test::class)->reveal();
        // has final methods, cannot be mocked
        $failure = new \Exception('Risky message');
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addRiskyTest($test, $failure, 1);

        $expectedContent = $this->encodeWithStartTestSuite([
            [
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ],
            [
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'error',
                'time' => 1,
                'trace' => [
                    [
                        'file' => __FILE__,
                        'line' => $line,
                    ],
                ],
                'message' => 'Risky Test: Risky message',
                'output' => '',
            ],
        ]);
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testAddSkippedTest()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize(Test::class)->reveal();
        // has final methods, cannot be mocked
        $failure = new \Exception('Skipped message');
        $line = __LINE__ - 1;

        $printer->startTest($test);
        $printer->addSkippedTest($test, $failure, 1);

        $expectedContent = $this->encodeWithStartTestSuite([
            [
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ],
            [
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'error',
                'time' => 1,
                'trace' => [
                    [
                        'file' => __FILE__,
                        'line' => $line,
                    ],
                ],
                'message' => 'Skipped Test: Skipped message',
                'output' => '',
            ],
        ]);
        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    public function testEndTest()
    {
        $printer = $this->createPrinterAndStartTestSuite();
        $test = $this->prophesize(Test::class)->reveal();

        $printer->startTest($test);
        $printer->endTest($test, 1);

        $expectedContent = $this->encodeWithStartTestSuite([
            [
                'event' => 'testStart',
                'suite' => get_class($this),
                'test' => get_class($test),
            ],
            [
                'event' => 'test',
                'suite' => get_class($this),
                'test' => get_class($test),
                'status' => 'pass',
                'time' => 1,
                'trace' => [],
                'message' => '',
                'output' => '',
            ],
        ]);
        $this->assertEquals($expectedContent, $this->getLogContent());

        $printer->endTestSuite($this->prophesize(TestSuite::class)->reveal());

        $this->assertEquals($expectedContent, $this->getLogContent());
    }

    private function createPrinterAndStartTestSuite(): LogPrinter
    {
        putenv(EnvVariables::LOG_DIR . '=' . sys_get_temp_dir());
        $printer = new LogPrinter();
        $testSuite = $this->prophesize(TestSuite::class);
        $testSuite->getName()
            ->willReturn(get_class($this));
        $testSuite->count()
            ->willReturn(1);

        $printer->startTestSuite($testSuite->reveal());

        return $printer;
    }

    private function getLogContent(): string
    {
        $logFilename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(__FILE__) . '.json.log';
        $this->assertFileExists($logFilename, 'Log file missing! Maybe you called this method too early?');

        $content = file_get_contents($logFilename);

        return preg_replace('/\r\n/', "\n", $content);
    }

    private function encodeWithStartTestSuite(array $data = []): string
    {
        $logElements = [$this->getStartTestSuiteLog()];
        foreach ($data as $datum) {
            $logElements[] = $datum;
        }

        $result = '';
        foreach ($logElements as $logElement) {
            $result .= json_encode($logElement, JSON_PRETTY_PRINT);
        }

        return $result;
    }

    private function getStartTestSuiteLog(): array
    {
        return [
            'event' => 'suiteStart',
            'suite' => get_class($this),
            'tests' => 1,
        ];
    }
}
