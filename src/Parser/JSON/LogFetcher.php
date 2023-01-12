<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Process\AbstractParaunitProcess;

class LogFetcher
{
    public const LOG_ENDING_STATUS = 'paraunitEnd';

    public function __construct(private readonly TempFilenameFactory $fileName)
    {
    }

    /**
     * @return \Generator<LogData>
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

        yield from LogData::parse($fileContent);
    }
}
