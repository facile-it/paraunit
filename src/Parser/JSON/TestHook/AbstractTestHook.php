<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Configuration\EnvVariables;

abstract class AbstractTestHook
{
    /** @var resource */
    protected static $logFile;

    public function __construct()
    {
        if (null === self::$logFile) {
            $file = fopen($this->getLogFilename(), 'wt');
            if (! \is_resource($file)) {
                throw new \RuntimeException('Unable to create log file');
            }

            self::$logFile = $file;
        }
    }

    protected function write(string $status, ?string $test, ?string $message): void
    {
        $data = [
            'status' => $status,
        ];

        if ($test) {
            $data['test'] = $this->convertToUtf8($test);
        }

        if ($message) {
            $data['message'] = $this->convertToUtf8($message);
        }

        \fwrite(self::$logFile, json_encode($data));
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
