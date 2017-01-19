<?php

namespace Paraunit\Parser\JSON;

/**
 * This class comes from \PHPUnit_Util_Log_JSON.
 * I't copied and refactored here because it's deprecated in PHPUnit 5.7 and it will be dropped in PHPUnit 6
 *
 * Class LogPrinter
 * @package Paraunit\Parser\JSON
 */
class LogPrinter extends \PHPUnit_Util_Printer implements \PHPUnit_Framework_TestListener
{
    /** @var string */
    private $logDirectory;

    /** @var int */
    private $testSuiteLevel;

    /**
     * LogPrinter constructor.
     * @param mixed $out
     */
    public function __construct($out = null)
    {
        if (substr($out, -1) !== DIRECTORY_SEPARATOR) {
            $out .= DIRECTORY_SEPARATOR;
        }

        $this->logDirectory = $out;
        $this->testSuiteLevel = 0;
    }

    /**
     * An error occurred.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                   $time
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->writeCase(
            'error',
            $time,
            \PHPUnit_Util_Filter::getFilteredStacktrace($e, false),
            \PHPUnit_Framework_TestFailure::exceptionToString($e),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * A warning occurred.
     *
     * @param \PHPUnit_Framework_Test    $test
     * @param \PHPUnit_Framework_Warning $e
     * @param float                      $time
     */
    public function addWarning(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_Warning $e, $time)
    {
        $this->writeCase(
            'warning',
            $time,
            \PHPUnit_Util_Filter::getFilteredStacktrace($e, false),
            \PHPUnit_Framework_TestFailure::exceptionToString($e),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * A failure occurred.
     *
     * @param \PHPUnit_Framework_Test                 $test
     * @param \PHPUnit_Framework_AssertionFailedError $e
     * @param float                                   $time
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->writeCase(
            'fail',
            $time,
            \PHPUnit_Util_Filter::getFilteredStacktrace($e, false),
            \PHPUnit_Framework_TestFailure::exceptionToString($e),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Incomplete test.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                   $time
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->writeCase(
            'error',
            $time,
            \PHPUnit_Util_Filter::getFilteredStacktrace($e, false),
            'Incomplete Test: ' . $e->getMessage(),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Risky test.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                   $time
     */
    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->writeCase(
            'error',
            $time,
            \PHPUnit_Util_Filter::getFilteredStacktrace($e, false),
            'Risky Test: ' . $e->getMessage(),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Skipped test.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                   $time
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->writeCase(
            'error',
            $time,
            \PHPUnit_Util_Filter::getFilteredStacktrace($e, false),
            'Skipped Test: ' . $e->getMessage(),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * A testsuite started.
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->testSuiteLevel === 0) {
            $logFilename = $this->getLogFilename($suite);

            $logDir = dirname($logFilename);
            if (! @mkdir($logDir, 0777, true) && !is_dir($logDir)) {
                throw new \RuntimeException('Cannot create folder for JSON logs');
            }

            $this->out = fopen($logFilename, 'wt');
        }

        $this->testSuiteLevel++;
        $this->currentTestSuiteName = $suite->getName();
        $this->currentTestName      = '';

        $this->write(
            array(
                'event' => 'suiteStart',
                'suite' => $this->currentTestSuiteName,
                'tests' => count($suite)
            )
        );
    }

    /**
     * A testsuite ended.
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->testSuiteLevel--;
        $this->currentTestSuiteName = '';
        $this->currentTestName      = '';
    }

    /**
     * A test started.
     *
     * @param \PHPUnit_Framework_Test $test
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        $this->currentTestName = \PHPUnit_Util_Test::describe($test);
        $this->currentTestPass = true;

        $this->write(
            array(
                'event' => 'testStart',
                'suite' => $this->currentTestSuiteName,
                'test'  => $this->currentTestName
            )
        );
    }

    /**
     * A test ended.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param float                  $time
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        if ($this->currentTestPass) {
            $this->writeCase('pass', $time, array(), '', $test);
        }
    }

    /**
     * @param string                           $status
     * @param float                            $time
     * @param array                            $trace
     * @param string                           $message
     * @param \PHPUnit_Framework_TestCase|null $test
     */
    protected function writeCase($status, $time, array $trace = array(), $message = '', $test = null)
    {
        $output = '';
        // take care of TestSuite producing error (e.g. by running into exception) as TestSuite doesn't have hasOutput
        if ($test !== null && method_exists($test, 'hasOutput') && $test->hasOutput()) {
            $output = $test->getActualOutput();
        }
        $this->write(
            array(
                'event'   => 'test',
                'suite'   => $this->currentTestSuiteName,
                'test'    => $this->currentTestName,
                'status'  => $status,
                'time'    => $time,
                'trace'   => $trace,
                'message' => \PHPUnit_Util_String::convertToUtf8($message),
                'output'  => $output,
            )
        );
    }

    /**
     * @param string $buffer
     */
    public function write($buffer)
    {
        array_walk_recursive($buffer, function (&$input) {
            if (is_string($input)) {
                $input = \PHPUnit_Util_String::convertToUtf8($input);
            }
        });

        parent::write(json_encode($buffer, JSON_PRETTY_PRINT));
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return string
     */
    private function getLogFilename(\PHPUnit_Framework_TestSuite $suite)
    {
        $testFilename = $this->getTestFilename($suite);
        
        return $this->logDirectory . md5($testFilename) . '.json.log';
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     * @return string
     */
    private function getTestFilename(\PHPUnit_Framework_TestSuite $suite)
    {
        $reflection = new \ReflectionClass($suite->getName());
        
        return $reflection->getFileName();
    }
}
