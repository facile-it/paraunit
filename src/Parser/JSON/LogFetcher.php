<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\AbstractParaunitProcess;

class LogFetcher
{
    public const LOG_ENDING_STATUS = 'paraunitEnd';

    /** @var TempFilenameFactory */
    private $fileName;

    public function __construct(TempFilenameFactory $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return Log[]
     */
    public function fetch(AbstractParaunitProcess $process): array
    {
        $filePath = $this->fileName->getFilenameForLog($process->getUniqueId());
        $fileContent = '';

        if (file_exists($filePath)) {
            /** @var string $fileContent */
            $fileContent = file_get_contents($filePath);
            unlink($filePath);
        }

        $logs = json_decode(self::cleanLog($fileContent), true, 10, JSON_THROW_ON_ERROR);

        return $this->createLogObjects($logs);
    }

    /**
     * @param string $jsonString The dirty output
     *
     * @return string            The normalized log, as an array of JSON objects
     */
    private static function cleanLog(string $jsonString): string
    {
        $splitted = str_replace('}{', '},{', $jsonString);

        return '[' . $splitted . ']';
    }

    /**
     * @param mixed[] $logs
     *
     * @return Log[]
     */
    private function createLogObjects(array $logs): array
    {
        $result = [];

        foreach ($logs as $log) {
            if (! array_key_exists('status', $log)) {
                throw new \InvalidArgumentException('Malformed logs');
            }

            $result[] = new Log($log['status'], $log['message'] ?? null);
        }

        $result[] = new Log(self::LOG_ENDING_STATUS);

        return $result;
    }
}
