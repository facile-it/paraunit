<?php

declare(strict_types=1);

namespace Tests\Stub\PHPUnitJSONLogOutput;

use Paraunit\Parser\JSON\Log;

class JSONLogStub
{
    public const TWO_ERRORS_TWO_FAILURES = '2Errors2Failures';

    public const ALL_GREEN = 'AllGreen';

    public const FATAL_ERROR = 'FatalError';

    public const SEGFAULT = 'SegFault';

    public const ONE_ERROR = 'SingleError';

    public const ONE_INCOMPLETE = 'SingleIncomplete';

    public const ONE_RISKY = 'SingleRisky';

    public const ONE_SKIP = 'SingleSkip';

    public const ONE_WARNING = 'SingleWarning';

    public const UNKNOWN = 'Unknown';

    public const PARSE_ERROR = 'ParseError';

    /**
     * @throws \Exception
     */
    public static function getLogs(string $filename): string
    {
        return json_decode(self::getCleanOutputFileContent($filename));
    }

    /**
     * @throws \Exception
     */
    public static function getCleanOutputFileContent(string $filename): string
    {
        $fullFilename = __DIR__ . DIRECTORY_SEPARATOR . $filename . '.json';
        if (! file_exists($fullFilename)) {
            throw new \Exception('Unknown file stub: ' . $filename);
        }
        /** @var string $rawLog */
        $rawLog = file_get_contents($fullFilename);

        return self::cleanLog($rawLog);
    }

    /**
     * @return Log[] The normalized log, as an array of JSON objects
     */
    private static function cleanLog(string $jsonString): string
    {
        $splitted = preg_replace('/\}\{/', '},{', $jsonString);

        return '[' . $splitted . ']';
    }
}
