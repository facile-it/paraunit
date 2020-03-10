<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Configuration\EnvVariables;

abstract class AbstractTestHook
{
    public const STATUS_ERROR = 'error';

    public const STATUS_WARNING = 'warning';

    public const STATUS_FAILURE = 'fail';

    public const STATUS_SUCCESSFUL = 'successful';

    public const STATUS_INCOMPLETE = 'incomplete';

    public const STATUS_SKIPPED = 'skipped';

    public const STATUS_RISKY = 'risky';

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

    protected function write(string $status, ?string $message, float $time): void
    {
        $data = [
            'status' => $status,
            'time' => $time,
        ];

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
