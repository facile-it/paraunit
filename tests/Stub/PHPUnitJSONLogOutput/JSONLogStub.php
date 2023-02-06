<?php

declare(strict_types=1);

namespace Tests\Stub\PHPUnitJSONLogOutput;

/**
 * @deprecated
 * TODO - remove this
 */
class JSONLogStub
{
    final public const TWO_ERRORS_TWO_FAILURES = '2Errors2Failures';

    final public const ALL_GREEN = 'AllGreen';

    final public const FATAL_ERROR = 'FatalError';

    final public const SEGFAULT = 'SegFault';

    final public const ONE_ERROR = 'SingleError';

    final public const ONE_INCOMPLETE = 'SingleIncomplete';

    final public const ONE_RISKY = 'SingleRisky';

    final public const ONE_SKIP = 'SingleSkip';

    final public const ONE_WARNING = 'SingleWarning';

    final public const ONE_DEPRECATION = 'SingleDeprecation';

    final public const UNKNOWN = 'Unknown';

    final public const PARSE_ERROR = 'ParseError';

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
     * @return non-empty-string The normalized log, as an array of JSON objects
     */
    private static function cleanLog(string $jsonString): string
    {
        $splitted = preg_replace('/\}\{/', '},{', $jsonString);

        return '[' . $splitted . ']';
    }
}
