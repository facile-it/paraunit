<?php

declare(strict_types=1);

namespace Paraunit\Logs\ValueObject;

class LogData implements \JsonSerializable
{
    public function __construct(
        public readonly LogStatus $status,
        public readonly Test $test,
        public readonly ?string $message,
        private readonly ?bool $ignoredByTest = null,
        private readonly ?bool $ignoredByBaseline = null,
    ) {}

    public function isIgnoredByTest(): bool
    {
        return $this->ignoredByTest ?? false;
    }

    public function isIgnoredByBaseline(): bool
    {
        return $this->ignoredByBaseline ?? false;
    }

    /**
     * @phpstan-assert array{
     *     status: string,
     *     test: string|array<mixed>,
     *     message?: string|null,
     *     ignoredByTest: bool|null,
     *     ignoredByBaseline: bool|null,
     * } $log
     */
    private static function validateLogFormat(mixed $log): void
    {
        if (! is_array($log)) {
            throw new \InvalidArgumentException('Expecting array from log entry, got ' . get_debug_type($log));
        }

        foreach (['status', 'test', 'ignoredByTest', 'ignoredByBaseline'] as $requiredKey) {
            if (! array_key_exists($requiredKey, $log)) {
                throw new \InvalidArgumentException('Missing fields in Paraunit logs');
            }
        }

        if (! is_string($log['status'])) {
            throw new \InvalidArgumentException('Invalid field status in Paraunit logs');
        }

        if (! is_string($log['message'] ?? '')) {
            throw new \InvalidArgumentException('Invalid field message in Paraunit logs');
        }

        if (! is_null($log['ignoredByTest']) && ! is_bool($log['ignoredByTest'])) {
            throw new \InvalidArgumentException('Invalid field message in Paraunit logs');
        }

        if (! is_null($log['ignoredByBaseline']) && ! is_bool($log['ignoredByBaseline'])) {
            throw new \InvalidArgumentException('Invalid field message in Paraunit logs');
        }
    }

    /**
     * @return array{status: string, test: string|array<string, scalar>, message?: string}
     */
    public function jsonSerialize(): array
    {
        $data = [
            'status' => $this->status->value,
            'test' => $this->test->jsonSerialize(),
            'ignoredByTest' => $this->ignoredByTest,
            'ignoredByBaseline' => $this->ignoredByBaseline,
        ];

        if (null !== $this->message) {
            $data['message'] = $this->message;
        }

        array_walk_recursive($data, $this->convertToUtf8(...));

        return $data;
    }

    private function convertToUtf8(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }

        if (! \mb_detect_encoding($string, 'UTF-8', true)) {
            return \mb_convert_encoding($string, 'UTF-8') ?: '';
        }

        return $string;
    }

    /**
     * @return list<self>
     */
    public static function parse(string $jsonLog): array
    {
        if ($jsonLog === '') {
            return [];
        }

        $decodedLogs = json_decode(self::cleanLog($jsonLog), true, 10, JSON_THROW_ON_ERROR);
        $logs = [];
        $lastTest = null;

        try {
            if (! is_array($decodedLogs)) {
                throw new \InvalidArgumentException('Expecting array from json_decode, got ' . get_debug_type($decodedLogs));
            }

            foreach ($decodedLogs as $log) {
                self::validateLogFormat($log);

                $logs[] = new self(
                    LogStatus::from($log['status']),
                    $lastTest = Test::deserialize($log['test']),
                    $log['message'] ?? null,
                    $log['ignoredByTest'] ?? null,
                    $log['ignoredByBaseline'] ?? null,
                );
            }
        } catch (\Throwable $e) {
            $logs[] = new self(
                LogStatus::Unknown,
                Test::unknown(),
                'Error while parsing Paraunit logs: ' . $e->getMessage(),
            );
        }

        $logs[] = new self(LogStatus::LogTerminated, $lastTest ?? Test::unknown(), null);

        return $logs;
    }

    /**
     * @param string $jsonString The dirty output
     *
     * @return non-empty-string The normalized log, as an array of JSON objects
     */
    private static function cleanLog(string $jsonString): string
    {
        $split = str_replace('}{', '},{', $jsonString);

        return '[' . $split . ']';
    }
}
