<?php
declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Configuration\EnvVariables;
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
    const STATUS_ERROR = 'error';
    const STATUS_WARNING = 'warning';
    const STATUS_FAIL = 'fail';
    const STATUS_PASS = 'pass';

    const MESSAGE_INCOMPLETE_TEST = 'Incomplete Test: ';
    const MESSAGE_RISKY_TEST = 'Risky Test: ';
    const MESSAGE_SKIPPED_TEST = 'Skipped Test: ';

    /** @var resource */
    private $logFile;

    /** @var string */
    private $currentTestSuiteName;

    /** @var string */
    private $currentTestName;

    /** @var bool */
    private $currentTestPass;

    public function __construct()
    {
        $this->logFile = fopen($this->getLogFilename(), 'wt');
        $this->autoFlush = true;
    }

    /**
     * An error occurred.
     *
     * @param Test $test
     * @param \Exception $exception
     * @param float $time
     */
    public function addError(Test $test, \Exception $exception, $time)
    {
        $this->writeCase(
            self::STATUS_ERROR,
            $time,
            $this->getStackTrace($exception),
            TestFailure::exceptionToString($exception),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * A warning occurred.
     *
     * @param Test $test
     * @param Warning $warning
     * @param float $time
     */
    public function addWarning(Test $test, Warning $warning, $time)
    {
        $this->writeCase(
            self::STATUS_WARNING,
            $time,
            $this->getStackTrace($warning),
            TestFailure::exceptionToString($warning),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * A failure occurred.
     *
     * @param Test $test
     * @param AssertionFailedError $error
     * @param float $time
     */
    public function addFailure(Test $test, AssertionFailedError $error, $time)
    {
        $this->writeCase(
            self::STATUS_FAIL,
            $time,
            $this->getStackTrace($error),
            TestFailure::exceptionToString($error),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Incomplete test.
     *
     * @param Test $test
     * @param \Exception $error
     * @param float $time
     */
    public function addIncompleteTest(Test $test, \Exception $error, $time)
    {
        $this->writeCase(
            self::STATUS_ERROR,
            $time,
            $this->getStackTrace($error),
            self::MESSAGE_INCOMPLETE_TEST . $error->getMessage(),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Risky test.
     *
     * @param Test $test
     * @param \Exception $exception
     * @param float $time
     */
    public function addRiskyTest(Test $test, \Exception $exception, $time)
    {
        $this->writeCase(
            self::STATUS_ERROR,
            $time,
            $this->getStackTrace($exception),
            self::MESSAGE_RISKY_TEST . $exception->getMessage(),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Skipped test.
     *
     * @param Test $test
     * @param \Exception $exception
     * @param float $time
     */
    public function addSkippedTest(Test $test, \Exception $exception, $time)
    {
        $this->writeCase(
            self::STATUS_ERROR,
            $time,
            $this->getStackTrace($exception),
            self::MESSAGE_SKIPPED_TEST . $exception->getMessage(),
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
            $this->writeCase(self::STATUS_PASS, $time, [], '', $test);
        }
    }

    /**
     * @param string $status
     * @param float $time
     * @param array $trace
     * @param string $message
     * @param Test|TestCase|null $test
     */
    private function writeCase($status, $time, array $trace = [], $message = '', $test = null)
    {
        $output = '';
        // take care of TestSuite producing error (e.g. by running into exception) as TestSuite doesn't have hasOutput
        if ($test !== null && method_exists($test, 'hasOutput') && $test->hasOutput()) {
            $output = $test->getActualOutput();
        }
        $this->writeArray([
            'event' => 'test',
            'suite' => $this->currentTestSuiteName,
            'test' => $this->currentTestName,
            'status' => $status,
            'time' => $time,
            'trace' => $trace,
            'message' => $this->convertToUtf8($message),
            'output' => $output,
        ]);
    }

    /**
     * @param array $buffer
     */
    private function writeArray($buffer)
    {
        array_walk_recursive($buffer, function (&$input) {
            if (is_string($input)) {
                $input = $this->convertToUtf8($input);
            }
        });

        $this->writeToLog(json_encode($buffer, JSON_PRETTY_PRINT));
    }

    private function writeToLog($buffer)
    {
        // ignore everything that is not a JSON object
        if ($buffer != '' && $buffer[0] === '{') {
            \fwrite($this->logFile, $buffer);
            \fflush($this->logFile);
        }
    }

    /**
     * @return string
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    private function getLogFilename(): string
    {
        $logDir = $this->getLogDirectory();
        if (! @mkdir($logDir, 0777, true) && ! is_dir($logDir)) {
            throw new \RuntimeException('Cannot create folder for JSON logs');
        }

        $logFilename = getenv(EnvVariables::PROCESS_UNIQUE_ID);
        if ($logFilename === false) {
            throw new \InvalidArgumentException('Log filename not received: environment variable not set');
        }

        return $logDir . $logFilename . '.json.log';
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getLogDirectory(): string
    {
        $logDirectory = getenv(EnvVariables::LOG_DIR);

        if ($logDirectory === false) {
            throw new \InvalidArgumentException('Log directory not received: environment variable not set');
        }

        if (substr($logDirectory, -1) !== DIRECTORY_SEPARATOR) {
            $logDirectory .= DIRECTORY_SEPARATOR;
        }

        return $logDirectory;
    }

    private function convertToUtf8($string): string
    {
        if (! \mb_detect_encoding($string, 'UTF-8', true)) {
            return \mb_convert_encoding($string, 'UTF-8');
        }

        return $string;
    }

    private function getStackTrace($error): array
    {
        /** @var string[] $trace */
        $trace = Util\Filter::getFilteredStacktrace($error, false);
        
        return $trace;
    }
}
