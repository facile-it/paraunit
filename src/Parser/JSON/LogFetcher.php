<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;

class LogFetcher
{
    public const LOG_ENDING_STATUS = 'paraunitEnd';

    public function __construct(private readonly TempFilenameFactory $fileName)
    {
    }

    /**
     * @return \Generator<Log>
     */
    public function fetch(AbstractParaunitProcess $process): \Generator
    {
        $filePath = $this->fileName->getFilenameForLog($process->getUniqueId());
        $fileContent = '';

        if (file_exists($filePath)) {
            /** @var string $fileContent */
            $fileContent = file_get_contents($filePath);
            unlink($filePath);
        }

        $logs = json_decode(self::cleanLog($fileContent), true, 10, JSON_THROW_ON_ERROR);

        yield from $this->createLogObjects($logs);
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
     * @return \Generator<Log>
     */
    private function createLogObjects(array $logs): \Generator
    {
        foreach ($logs as $log) {
            try {
                yield new Log(
                    TestStatus::from($log['status'] ?? null),
                    new Test($log['test'] ?? 'N/A'),
                    $log['message'] ?? null
                );
            } catch (\ValueError $valueError) {
                throw new \InvalidArgumentException('Malformed logs', 500, $valueError);
            }
        }
    }
}
