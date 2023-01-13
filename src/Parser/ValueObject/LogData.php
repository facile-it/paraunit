<?php

declare(strict_types=1);

namespace Paraunit\Parser\ValueObject;

use PHPUnit\Event\Code\Test as PHPUnitTest;

class LogData implements \JsonSerializable
{
    public readonly Test $test;

    public function __construct(
        public readonly TestStatus $status,
        PHPUnitTest|Test $test,
        public readonly ?string $message
    ) {
        $this->test = $test instanceof Test
            ? $test
            : Test::fromPHPUnitTest($test);
    }

    /**
     * @return array{status: string, test: string, message?: string}
     */
    public function jsonSerialize(): array
    {
        $data = [
            'status' => $this->status->value,
            'test' => $this->test->name,
        ];

        if ($this->message) {
            $data['message'] = $this->message;
        }

        // TODO: test UTF8 conversion
        return array_map($this->convertToUtf8(...), $data);
    }

    private function convertToUtf8(string $string): string
    {
        if (! \mb_detect_encoding($string, 'UTF-8', true)) {
            return \mb_convert_encoding($string, 'UTF-8');
        }

        return $string;
    }

    /**
     * @return self[]
     */
    public static function parse(string $jsonLog): array
    {
        $decodedLogs = json_decode(self::cleanLog($jsonLog), true, 10, JSON_THROW_ON_ERROR);
        $logs = [];

        foreach ($decodedLogs as $log) {
            $logs[] = new self(
                TestStatus::from($log['status']),
                $lastTest = new Test($log['test']),
                $log['message'] ?? null,
            );
        }

        $logs[] = new self(TestStatus::LogTerminated, $lastTest ?? Test::unknown(), null);

        return $logs;
    }

    /**
     * @param string $jsonString The dirty output
     *
     * @return string            The normalized log, as an array of JSON objects
     */
    private static function cleanLog(string $jsonString): string
    {
        $split = str_replace('}{', '},{', $jsonString);

        return '[' . $split . ']';
    }
}
