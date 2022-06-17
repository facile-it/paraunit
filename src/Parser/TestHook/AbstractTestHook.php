<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Parser\DTO\TestStatus;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;

abstract class AbstractTestHook
{
    /** @var resource */
    protected static $logFile;

    public function __construct()
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (null === self::$logFile) {
            $file = fopen($this->getLogFilename(), 'wt');
            if (! \is_resource($file)) {
                throw new \RuntimeException('Unable to create log file');
            }

            self::$logFile = $file;
        }
    }

    final protected function write(TestStatus $status, Test $test, ?string $message): void
    {
        $data = [
            'status' => $status->value,
            'test' => $test instanceof TestMethod
                ? $test->className() . '::' . $test->name()
                : $test->file(),
            'message' => $message,
        ];

        $data = array_map([$this, 'convertToUtf8'], $data);

        \fwrite(self::$logFile, json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
        \fflush(self::$logFile);
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
}
