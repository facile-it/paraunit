<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

class Log
{
    public const STATUS_TEST_START = 'testStart';

    public const STATUS_AFTER_LAST_TEST = 'lastTestEnd';

    public const STATUS_ERROR = 'error';

    public const STATUS_FAILURE = 'fail';

    public const STATUS_INCOMPLETE = 'incomplete';

    public const STATUS_RISKY = 'risky';

    public const STATUS_SKIPPED = 'skipped';

    public const STATUS_SUCCESSFUL = 'successful';

    public const STATUS_WARNING = 'warning';

    /** @var string */
    private $status;

    /** @var string|null */
    private $message;

    public function __construct(string $status, ?string $message = null)
    {
        $this->status = $status;
        $this->message = $message;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
