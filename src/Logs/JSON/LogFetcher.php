<?php

declare(strict_types=1);

namespace Paraunit\Logs\JSON;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Process\AbstractParaunitProcess;

class LogFetcher
{
    public function __construct(private readonly TempFilenameFactory $fileName)
    {
    }

    /**
     * @return list<LogData>
     */
    public function fetch(AbstractParaunitProcess $process): array
    {
        $filePath = $this->fileName->getFilenameForLog($process->getUniqueId());

        if (! file_exists($filePath)) {
            return [];
        }

        /** @var string $fileContent */
        $fileContent = file_get_contents($filePath);
        unlink($filePath);

        return LogData::parse($fileContent);
    }
}
