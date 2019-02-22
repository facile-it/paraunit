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
     * @return \stdClass[]
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

        $logs = json_decode(self::cleanLog($fileContent));
        $logs[] = $this->createLogEnding();

        return $logs;
    }

    /**
     * @param string $jsonString The dirty output
     *
     * @return string            The normalized log, as an array of JSON objects
     */
    private static function cleanLog(string $jsonString): string
    {
        $splitted = preg_replace('/\}\{/', '},{', $jsonString);

        return '[' . $splitted . ']';
    }

    private function createLogEnding(): \stdClass
    {
        $logEnding = new \stdClass();
        $logEnding->status = self::LOG_ENDING_STATUS;

        return $logEnding;
    }
}
