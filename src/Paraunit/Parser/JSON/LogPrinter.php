<?php

namespace Paraunit\Parser\JSON;

use Paraunit\Configuration\StaticOutputPath;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Util;

/**
 * This class comes from Util\Log_JSON.
 * It's copied and refactored here because it's deprecated in PHPUnit 5.7 and it will be dropped in PHPUnit 6
 *
 * Class LogPrinter
 * @package Paraunit\Parser\JSON
 */
class LogPrinter extends Util\Printer implements TestListener
{
    /** @var string */
    private $logDirectory;

    /** @var int */
    private $testSuiteLevel;

    /** @var string */
    private $currentTestSuiteName;

    /** @var string */
    private $currentTestName;

    /** @var bool */
    private $currentTestPass;

    public function __construct()
    {
        $this->testSuiteLevel = 0;
    }

    /**
     * An error occurred.
     *
     * @param Test $test
     * @param \Exception $e
     * @param float $time
     */
    public function addError(Test $test, \Exception $e, $time)
    {
        $this->writeCase(
            'error',
            $time,
            Util\Filter::getFilteredStacktrace($e, false),
            TestFailure::exceptionToString($e),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * A warning occurred.
     *
     * @param Test $test
     * @param Warning $e
     * @param float $time
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
        $this->writeCase(
            'warning',
            $time,
            Util\Filter::getFilteredStacktrace($e, false),
            TestFailure::exceptionToString($e),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * A failure occurred.
     *
     * @param Test $test
     * @param AssertionFailedError $e
     * @param float $time
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->writeCase(
            'fail',
            $time,
            Util\Filter::getFilteredStacktrace($e, false),
            TestFailure::exceptionToString($e),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Incomplete test.
     *
     * @param Test $test
     * @param \Exception $e
     * @param float $time
     */
    public function addIncompleteTest(Test $test, \Exception $e, $time)
    {
        $this->writeCase(
            'error',
            $time,
            Util\Filter::getFilteredStacktrace($e, false),
            'Incomplete Test: ' . $e->getMessage(),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Risky test.
     *
     * @param Test $test
     * @param \Exception $e
     * @param float $time
     */
    public function addRiskyTest(Test $test, \Exception $e, $time)
    {
        $this->writeCase(
            'error',
            $time,
            Util\Filter::getFilteredStacktrace($e, false),
            'Risky Test: ' . $e->getMessage(),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Skipped test.
     *
     * @param Test $test
     * @param \Exception $e
     * @param float $time
     */
    public function addSkippedTest(Test $test, \Exception $e, $time)
    {
        $this->writeCase(
            'error',
            $time,
            Util\Filter::getFilteredStacktrace($e, false),
            'Skipped Test: ' . $e->getMessage(),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * A testsuite started.
     *
     * @param TestSuite $suite
     * @throws \RuntimeException
     */
    public function startTestSuite(TestSuite $suite)
    {
        if ($this->testSuiteLevel === 0) {
            $logFilename = $this->getLogFilename($suite);

            $logDir = dirname($logFilename);
            if (! @mkdir($logDir, 0777, true) && ! is_dir($logDir)) {
                throw new \RuntimeException('Cannot create folder for JSON logs');
            }

            $this->out = fopen($logFilename, 'wt');
        }

        $this->testSuiteLevel++;
        $this->currentTestSuiteName = $suite->getName();
        $this->currentTestName = '';

        $this->writeArray([
            'event' => 'suiteStart',
            'suite' => $this->currentTestSuiteName,
            'tests' => count($suite)
        ]);
    }

    public function endTestSuite(TestSuite $suite)
    {
        $this->testSuiteLevel--;
        $this->currentTestSuiteName = '';
        $this->currentTestName = '';
    }

    public function startTest(Test $test)
    {
        $this->currentTestName = Util\Test::describe($test);
        $this->currentTestPass = true;

        $this->writeArray([
            'event' => 'testStart',
            'suite' => $this->currentTestSuiteName,
            'test' => $this->currentTestName
        ]);
    }

    /**
     * A test ended.
     *
     * @param Test $test
     * @param float $time
     */
    public function endTest(Test $test, $time)
    {
        if ($this->currentTestPass) {
            $this->writeCase('pass', $time, array(), '', $test);
        }
    }

    /**
     * @param string $status
     * @param float $time
     * @param array $trace
     * @param string $message
     * @param TestCase|null $test
     */
    protected function writeCase($status, $time, array $trace = array(), $message = '', $test = null)
    {
        $output = '';
        // take care of TestSuite producing error (e.g. by running into exception) as TestSuite doesn't have hasOutput
        if ($test !== null && method_exists($test, 'hasOutput') && $test->hasOutput()) {
            $output = $test->getActualOutput();
        }
        $this->writeArray(
            array(
                'event' => 'test',
                'suite' => $this->currentTestSuiteName,
                'test' => $this->currentTestName,
                'status' => $status,
                'time' => $time,
                'trace' => $trace,
                'message' => $this->convertToUtf8($message),
                'output' => $output,
            )
        );
    }

    /**
     * @param array $buffer
     */
    public function writeArray($buffer)
    {
        array_walk_recursive($buffer, function (&$input) {
            if (is_string($input)) {
                $input = $this->convertToUtf8($input);
            }
        });

        $this->write(json_encode($buffer, JSON_PRETTY_PRINT));
    }

    private function getLogFilename(TestSuite $suite): string
    {
        $testFilename = $this->getTestFilename($suite);

        return $this->getLogDirectory() . md5($testFilename) . '.json.log';
    }

    private function getTestFilename(TestSuite $suite): string
    {
        $reflection = new \ReflectionClass($suite->getName());

        return $reflection->getFileName();
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getLogDirectory(): string
    {
        $this->logDirectory = StaticOutputPath::getPath();
        if (substr($this->logDirectory, -1) !== DIRECTORY_SEPARATOR) {
            $this->logDirectory .= DIRECTORY_SEPARATOR;
        }

        return $this->logDirectory;
    }

    private function convertToUtf8($string): string
    {
        if (! $this->isUtf8($string)) {
            if (\function_exists('mb_convert_encoding')) {
                return \mb_convert_encoding($string, 'UTF-8');
            }

            return \utf8_encode($string);
        }

        return $string;
    }

    private function isUtf8(string $string): bool
    {
        $length = \strlen($string);

        for ($i = 0; $i < $length; $i++) {
            if (\ord($string[$i]) < 0x80) {
                $n = 0;
            } elseif ((\ord($string[$i]) & 0xE0) == 0xC0) {
                $n = 1;
            } elseif ((\ord($string[$i]) & 0xF0) == 0xE0) {
                $n = 2;
            } elseif ((\ord($string[$i]) & 0xF0) == 0xF0) {
                $n = 3;
            } else {
                return false;
            }

            for ($j = 0; $j < $n; $j++) {
                if ((++$i == $length) || ((\ord($string[$i]) & 0xC0) != 0x80)) {
                    return false;
                }
            }
        }

        return true;
    }
}
