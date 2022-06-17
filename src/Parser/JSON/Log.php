<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Parser\DTO\Test;
use Paraunit\Parser\DTO\TestStatus;

class Log
{
    public function __construct(
        public readonly TestStatus $status,
        public readonly Test $test,
        public readonly ?string $message,
    ) {
    }
}
