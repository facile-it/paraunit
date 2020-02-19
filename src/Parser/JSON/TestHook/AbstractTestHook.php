<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Configuration\EnvVariables;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\SelfDescribing;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Util;

abstract class AbstractTestHook
{
    public const STATUS_ERROR = 'error';

    public const STATUS_WARNING = 'warning';

    public const STATUS_FAILURE = 'fail';

    public const STATUS_SUCCESSFUL = 'successful';

    public const STATUS_INCOMPLETE = 'incomplete';

    public const STATUS_SKIPPED = 'skipped';

    public const STATUS_RISKY = 'risky';

    public const MESSAGE_INCOMPLETE_TEST = 'Incomplete Test: ';

    public const MESSAGE_RISKY_TEST = 'Risky Test: ';

    public const MESSAGE_SKIPPED_TEST = 'Skipped Test: ';

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
        $file = fopen($this->getLogFilename(), 'wt');
        if (! \is_resource($file)) {
            throw new \RuntimeException('Unable to create log file');
        }
        $this->logFile = $file;
        $this->autoFlush = true;
    }

    /**
     * An error occurred.
     */
    public function addError(Test $test, \Throwable $exception, float $time): void
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
     */
    public function addWarning(Test $test, Warning $warning, float $time): void
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
     */
    public function addFailure(Test $test, AssertionFailedError $error, float $time): void
    {
        $this->writeCase(
            self::STATUS_FAILURE,
            $time,
            $this->getStackTrace($error),
            TestFailure::exceptionToString($error),
            $test
        );

        $this->currentTestPass = false;
    }

    /**
     * Incomplete test.
     */
    public function addIncompleteTest(Test $test, \Throwable $error, float $time): void
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
     */
    public function addRiskyTest(Test $test, \Throwable $exception, float $time): void
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
     */
    public function addSkippedTest(Test $test, \Throwable $exception, float $time): void
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
     * @throws \RuntimeException
     */
    public function startTestSuite(TestSuite $suite): void
    {
        $this->currentTestSuiteName = $suite->getName();
        $this->currentTestName = '';

        $this->writeArray([
            'event' => 'suiteStart',
            'suite' => $this->currentTestSuiteName,
            'tests' => count($suite),
        ]);
    }

    public function endTestSuite(TestSuite $suite): void
    {
        $this->currentTestSuiteName = '';
        $this->currentTestName = '';
    }

    public function startTest(Test $test): void
    {
        $this->currentTestName = $test instanceof SelfDescribing ? $test->toString() : \get_class($test);
        $this->currentTestPass = true;

        $this->writeArray([
            'event' => 'testStart',
            'suite' => $this->currentTestSuiteName,
            'test' => $this->currentTestName,
        ]);
    }

    /**
     * A test ended.
     */
    public function endTest(Test $test, float $time): void
    {
        if ($this->currentTestPass) {
            $this->writeCase(self::STATUS_SUCCESSFUL, $time, '', '', $test);
        }
    }

    protected function write(string $status, string $message, float $time): void
    {
        $buffer = json_encode([
            'status' => $status,
            'message' => $this->convertToUtf8($message),
            'time' => $time,
        ]);

        \fwrite($this->logFile, $buffer);
        \fflush($this->logFile);
    }

    /**
     * @deprecated
     *
     * @param Test|TestCase|null $test
     */
    protected function writeCase(string $status, float $time, string $trace, string $message = '', $test = null): void
    {
        $output = '';
        if ($test instanceof TestCase) {
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
     * @deprecated
     *
     * @param (mixed|string)[] $buffer
     */
    private function writeArray($buffer): void
    {
        array_walk_recursive($buffer, function (&$input) {
            if (is_string($input)) {
                $input = $this->convertToUtf8($input);
            }
        });

        $this->writeToLog(json_encode($buffer, JSON_PRETTY_PRINT));
    }

    /**
     * @deprecated
     *
     * @param string|false $buffer
     */
    private function writeToLog($buffer): void
    {
        // ignore everything that is not a JSON object
        if ($buffer && $buffer[0] === '{') {
            \fwrite($this->logFile, $buffer);
            \fflush($this->logFile);
        }
    }

    /**
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

    private function convertToUtf8(string $string): string
    {
        if (! \mb_detect_encoding($string, 'UTF-8', true)) {
            return \mb_convert_encoding($string, 'UTF-8');
        }

        return $string;
    }

    protected function getStackTrace(\Throwable $error): string
    {
        return Util\Filter::getFilteredStacktrace($error);
    }
}
